<?php
namespace TYPO3\Docs\RenderingHub\Job\Build;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Job to render a single documentation for a Git repository.
 *
 */
class GitDocumentJob implements \TYPO3\Jobqueue\Common\Job\JobInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\Git\PackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * The document
	 *
	 * @var \TYPO3\Docs\RenderingHub\Domain\Model\Document
	 */
	protected $document;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\UserLogger
	 */
	protected $userLogger;

	/**
	 * The input directory where to find the document
	 *
	 * @var string
	 */
	protected $inputDirectory;

	/**
	 * The output directory where to write the document
	 *
	 * @var string
	 */
	protected $outputDirectory;

	/**
	 * The temporary directory where to write temp data
	 *
	 * @var string
	 */
	protected $temporaryDirectory;

	/**
	 * The complete path to the warnings file
	 *
	 * @var string
	 */
	protected $warningFile;

	/**
	 * Whether command should be executed or displayed
	 * @todo should be handled by AOP
	 *
	 * @var boolean
	 */
	protected $dryRun = FALSE;

	/**
	 * Status of the document
	 *
	 * @var string
	 */
	protected $status = '';

	/**
	 * The formats to be outputted
	 *
	 * @var array
	 */
	protected $formats = array('html');

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Directory
	 */
	protected $directoryFinder;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Service\Sync\JobService
	 */
	protected $syncJobService;

	/**
	 * Settings injection
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Constructor
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Document $document
	 */
	public function __construct(\TYPO3\Docs\RenderingHub\Domain\Model\Document $document) {
		$this->document = $document;
	}

	/**
	 * Initialize the environment
	 *
	 * @return void
	 */
	protected function initialize() {
		$this->inputDirectory = $this->directoryFinder->getSource($this->document);
		$this->outputDirectory = $this->directoryFinder->getBuild($this->document);
		$this->temporaryDirectory = $this->directoryFinder->getTemporary($this->document);
		$this->warningFile = $this->outputDirectory . '/Warnings.txt';

		// Whether command should be only executed or displayed on the console
		\TYPO3\Docs\RenderingHub\Utility\Console::$dryRun = $this->dryRun;
	}

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
	 * @param \TYPO3\Jobqueue\Common\Queue\QueueInterface $queue
	 * @param \TYPO3\Jobqueue\Common\Queue\Message $message The original message
	 * @return boolean TRUE if the job was executed successfully and the message should be finished
	 */
	public function execute(\TYPO3\Jobqueue\Common\Queue\QueueInterface $queue, \TYPO3\Jobqueue\Common\Queue\Message $message) {
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
			$this->status = \TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_NOT_FOUND;
			\TYPO3\Flow\Utility\Files::removeDirectoryRecursively($this->outputDirectory); # Clean up file structure
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
	protected function renderDocumentAndLog () {

		if (!$this->dryRun) {

			# Runs command and log
			$this->systemLogger->log('Git: starting rendering document for ' . $this->document->getUri(), LOG_INFO);
			\TYPO3\Docs\RenderingHub\Utility\Console::run($this->getMakeCleanCommand());
			\TYPO3\Docs\RenderingHub\Utility\Console::run($this->getMakeHtmlCommand());
			$this->systemLogger->log('Git: rendering finished: ', LOG_INFO);
			$this->systemLogger->log('     -> rendered document located at ' . $this->outputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> source files located at ' . $this->inputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> temporary files located at ' . $this->temporaryDirectory, LOG_INFO);
			$this->sendWarningToAuthors();
			$this->sendAlertToMaintainers();

			$this->status = \TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_SYNC;
		} else {
			\TYPO3\Docs\RenderingHub\Utility\Console::output($this->getMakeCleanCommand());
			\TYPO3\Docs\RenderingHub\Utility\Console::output($this->getMakeHtmlCommand());
		}
	}

	/**
	 * Send warnings generated during rendering process to the authors of the document
	 *
	 * @return void
	 */
	protected function sendWarningToAuthors() {
		if (file_exists($this->warningFile) && filesize($this->warningFile) > 0) {

			// Computes variables
			$message = sprintf('[docs.typo3.org] Documentation rendering has generated warnings for package "%s" version "%s"',
				$this->document->getPackageKey(),
				$this->document->getVersion()
			);
			$content = file_get_contents($this->warningFile);
			$recipients = $this->settings['maintainers'];// @todo change that to real authors

			// Properly send notification
			$this->systemLogger->log('     -> warnings found and logged at ' . $this->warningFile, LOG_INFO);
			$this->systemLogger->log('     -> sending warnings to document authors: ' . implode(', ', $recipients), LOG_INFO);
			$this->userLogger->log($message, $content, $recipients);
		}
	}

	/**
	 * Send a possible alert to the maintainer if an Exception was found
	 *
	 * @return void
	 */
	protected function sendAlertToMaintainers() {
		if (file_exists($this->warningFile) && filesize($this->warningFile) > 0) {
			$content = file_get_contents($this->warningFile);

			// Detect string "Exception occurred" in Warnings file
			if (preg_match('/Exception/isU', $content)) {
				$_content = "Something went wrong when rendering \"{$this->document->getPackageKey()}\" version \"{$this->document->getVersion()}\":\n";
				$_content .= "- target document located at {$this->outputDirectory}\n";
				$_content .= "- source files located at {$this->inputDirectory}\n";
				$_content .= "- temporary files located at {$this->temporaryDirectory}\n\n";
				$_content .= "Server sending the message: " . gethostname() . "\n\n";

				$this->systemLogger->log($_content . chr(10) . $content, LOG_ALERT);
				$this->systemLogger->log('This message seems serious, an email was sent to the maintainers', LOG_INFO);
			}
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
		} elseif ($this->document->getStatus() === \TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_RENDER) { // TRUE when flag "dry-run" is set and new document
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

	/**
	 * @param boolean $dryRun
	 */
	public function setDryRun($dryRun) {
		$this->dryRun = $dryRun;
	}

	/**
	 * @param array $formats
	 */
	public function setFormats(array $formats) {
		$this->formats = $formats;
	}
}
?>