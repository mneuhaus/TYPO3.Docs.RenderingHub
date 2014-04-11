<?php
namespace TYPO3\Docs\RenderingHub\Job\Build;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Docs\RenderingHub\Domain\Model\Document;
use TYPO3\Docs\RenderingHub\Utility\Console;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Files;
use TYPO3\Jobqueue\Common\Queue\Message;
use TYPO3\Jobqueue\Common\Queue\QueueInterface;

/**
 * Job to render a single documentation for a Git repository.

 */
class TerDocumentJob extends AbstractDocumentJob {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\Ter\PackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * Where to find the directory containing the make
	 *
	 * @var string
	 */
	protected $makeDirectory;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\File
	 */
	protected $fileFinder;

	/**
	 * Initialize the environment
	 *
	 * @return void
	 */
	protected function initialize() {
		parent::initialize();
		$this->makeDirectory = $this->outputDirectory . '/t3pdb/Documentation/_make';
		$this->warningFile = $this->makeDirectory . "/Warnings.txt";
	}

	/**
	 * Execute the job
	 * A job should finish itself after successful execution using the queue methods.
	 *
	 * @param QueueInterface $queue
	 * @param Message $message The original message
	 * @return boolean TRUE if the job was executed successfully and the message should be finished
	 */
	public function execute(QueueInterface $queue, Message $message) {
		$this->systemLogger->log("Ter: processing {$this->document->getPackageKey()} {$this->document->getVersion()}");
		$this->initialize();
		$this->prepareSource();

		if (file_exists($this->inputDirectory . '/Documentation/Index.rst')) {
			$this->status = Document::STATUS_SYNC;
			// @todo call Martin's script 1
			$this->systemLogger->log('not implemented!!! - Ter: rendering completed for document ' . $this->document->getUri(), LOG_INFO);
		} elseif (file_exists($this->inputDirectory . '/doc/manual.sxw')) {
			$this->renderLegacyDocumentAndLog();

			$job = $this->syncJobService->create($this->document->getPackageKey());
			$this->syncJobService->queue($job);
		} else {
			$this->status = Document::STATUS_NOT_FOUND;
			Files::removeDirectoryRecursively($this->outputDirectory);
			$this->systemLogger->log('Ter: nothing to render for document ' . $this->document->getUri(), LOG_INFO);
		}

		$this->persistAndCleanUp();
		$this->systemLogger->log("-----------------------------------------------");

		return TRUE;
	}

	/**
	 * Fetch the source of the documentation
	 *
	 * @return void
	 */
	protected function prepareSource() {
		$packageFile = $this->fileFinder->getExtensionFileNameAndPath($this->document);
		$packageRemoteFile = $this->fileFinder->getRemoteFile($this->document);

		$this->terPackageRepository->fetch($packageFile, $packageRemoteFile);
		$this->terPackageRepository->extract($packageFile, $this->inputDirectory);
	}

	/**
	 * Render a legacy document and log what is necessary
	 *
	 * @return void
	 */
	protected function renderLegacyDocumentAndLog() {

		if (!$this->dryRun) {

			# Runs command and log
			$this->systemLogger->log('Ter: starting rendering document for ' . $this->document->getUri(), LOG_INFO);
			Console::run($this->getSxw2HtmlCommand());

			$this->overwriteMake(); // should be temporary
			Console::run($this->getMakeCleanCommand());
			Console::run($this->getMakeHtmlCommand());

			$this->systemLogger->log('Ter: rendering finished: ', LOG_INFO);
			$this->systemLogger->log('     -> rendered document located at ' . $this->outputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> source files located at ' . $this->inputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> temporary files located at ' . $this->makeDirectory, LOG_INFO);
			$this->sendAlertToMaintainers();

			$this->status = Document::STATUS_SYNC;
		} else {
			Console::output($this->getSxw2HtmlCommand());
			Console::output($this->getMakeCleanCommand());
			Console::output($this->getMakeHtmlCommand());
		}
	}

	/**
	 * Should be temporary measure prior to update Make file in package TYPO3.RestTools
	 *
	 * @return void
	 */
	protected function overwriteMake() {
		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename('resource://TYPO3.Docs.RenderingHub/Private/Templates/Build/Makefile.legacy.fluid');
		Files::createDirectoryRecursively($this->makeDirectory);

		$makeFilePathAndFilename = $this->makeDirectory . '/Makefile';
		file_put_contents($makeFilePathAndFilename, $view->render());
	}

	/**
	 * Persist and clean up environment
	 *
	 * @return void
	 */
	protected function persistAndCleanUp() {
		if (!$this->dryRun) {
			$this->documentRepository->resetUriAlias($this->document->getPackageKey());
			$this->document->setStatus($this->status);
			$this->documentRepository->update($this->document);

			// Persist "manually"
			$this->persistenceManager->persistAll();
		} elseif ($this->document->getStatus() === Document::STATUS_RENDER) { // TRUE when flag "dry-run" is set and new document
			$this->documentRepository->remove($this->document);
			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * Generate the sxw2html command that will transform manual.sxw to reST
	 *
	 * @return string
	 */
	protected function getSxw2HtmlCommand() {
		$package = $this->packageManager->getPackage('TYPO3.RestTools')->getPackagePath();
		$command = sprintf('cd %s; /usr/bin/python %s %s/doc/manual.sxw %s 2>&1 >> %sLogs/sxw2html.log',
			$package . $this->settings['sxw2htmlPath'],
			$this->settings['sxw2html'],
			$this->inputDirectory,
			$this->outputDirectory,
			FLOW_PATH_DATA);

		return $command;
	}

	/**
	 * Generate the Make clean command that will clean the docs
	 *
	 * @return string the command
	 */
	protected function getMakeCleanCommand() {
		$command = sprintf('cd %s; make clean --quiet',
			$this->makeDirectory
		);

		return $command;
	}

	/**
	 * Generate the Make html command that will make the docs
	 *
	 * @return string the command
	 */
	protected function getMakeHtmlCommand() {
		$command = sprintf('cd %s; make html --quiet 2> %s',
			$this->makeDirectory,
			$this->warningFile
		);

		return $command;
	}

	/**
	 * Get the identifier
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return 'ter';
	}

	/**
	 * Get the label
	 *
	 * @return string
	 */
	public function getLabel() {
		return 'Ter Document job ' . $this->inputDirectory;
	}
}
