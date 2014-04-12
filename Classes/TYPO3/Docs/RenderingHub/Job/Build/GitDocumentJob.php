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
 *
 */
class GitDocumentJob extends AbstractDocumentJob {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\Git\PackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * Fetch the source of the documentation
	 *
	 * @return void
	 */
	protected function prepareSource() {
		$this->gitPackageRepository->fetch($this->inputDirectory, $this->document->getRepository());
		$this->gitPackageRepository->checkout($this->inputDirectory, $this->document->getRepositoryTag());
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
		$this->systemLogger->log("Git: processing {$this->document->getPackageKey()} {$this->document->getVersion()}");
		$this->initialize();
		$this->prepareSource();

		if (file_exists($this->inputDirectory . '/Documentation/Index.rst') || $this->dryRun) {

			$this->writeConfFile();
			$this->writeMakeFile();
			$this->renderDocumentAndLog();

			// Create a new job for synchronizing files
			$job = $this->syncJobService->create($this->document->getPackageKey());
			$this->syncJobService->queue($job);
		} else {
			$this->status = Document::STATUS_NOT_FOUND;
			Files::removeDirectoryRecursively($this->outputDirectory); # Clean up file structure
			$this->systemLogger->log('Git: nothing to render for document ' . $this->document->getUri(), LOG_INFO);
		}

		$this->persistAndCleanUp();
		$this->systemLogger->log("-----------------------------------------------");

		return TRUE;
	}

	/**
	 * Render a document and log what is necessary
	 *
	 * @return void
	 */
	protected function renderDocumentAndLog() {

		if (!$this->dryRun) {

			# Runs command and log
			$this->systemLogger->log('Git: starting rendering document for ' . $this->document->getUri(), LOG_INFO);
			Console::run($this->getMakeCleanCommand());
			Console::run($this->getMakeHtmlCommand());
			$this->systemLogger->log('Git: rendering finished: ', LOG_INFO);
			$this->systemLogger->log('     -> rendered document located at ' . $this->outputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> source files located at ' . $this->inputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> temporary files located at ' . $this->temporaryDirectory, LOG_INFO);
			$this->sendWarningToAuthors();
			$this->sendAlertToMaintainers();

			$this->status = Document::STATUS_SYNC;
		} else {
			Console::output($this->getMakeCleanCommand());
			Console::output($this->getMakeHtmlCommand());
		}
	}

	/**
	 * Persist and clean up environment
	 *
	 * @return void
	 */
	protected function persistAndCleanUp() {

		// Persist only if not dry-run mode
		if (!$this->dryRun) {
			$this->document->setStatus($this->status);
			$this->documentRepository->update($this->document);

			// Persist "manually"
			$this->persistenceManager->persistAll();

			// Clean up file structure
			#\TYPO3\Flow\Utility\Files::removeDirectoryRecursively($this->temporaryDirectory);
		} elseif ($this->document->getStatus() === Document::STATUS_RENDER) { // TRUE when flag "dry-run" is set and new document
			$this->documentRepository->remove($this->document);
			$this->persistenceManager->persistAll();
		}
	}

	/**
	 * Generate the Conf.py configuration file
	 *
	 * @return void
	 */
	protected function writeMakeFile() {
		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename('resource://TYPO3.Docs.RenderingHub/Private/Templates/Build/Makefile.fluid');
		$view->assign('inputDirectory', $this->inputDirectory . '/Documentation');
		$view->assign('outputDirectory', $this->outputDirectory);
		file_put_contents($this->temporaryDirectory . '/Makefile', $view->render());
	}

	/**
	 * Generate the Conf.py configuration file
	 *
	 * @return void
	 */
	protected function writeConfFile() {
		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename('resource://TYPO3.Docs.RenderingHub/Private/Templates/Build/conf.py.fluid');
		$view->assign('inputDirectory', $this->inputDirectory);
		$view->assign('outputDirectory', $this->outputDirectory);
		file_put_contents($this->temporaryDirectory . '/conf.py', $view->render());
	}

	/**
	 * Generate the Make clean command that will clean the docs
	 *
	 * @return string
	 */
	protected function getMakeCleanCommand() {
		$command = sprintf('cd %s; make clean --quiet',
			$this->temporaryDirectory
		);

		return $command;
	}

	/**
	 * Generate the Make html command that will make the docs
	 *
	 * @return string
	 */
	protected function getMakeHtmlCommand() {
		$command = sprintf('cd %s; make html --quiet 2> %s',
			$this->temporaryDirectory,
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
		return 'git';
	}

	/**
	 * Get the label
	 *
	 * @return string
	 */
	public function getLabel() {
		return 'Git Document job ' . $this->inputDirectory;
	}
}
