<?php
namespace TYPO3\Docs\Job\Build;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Job to render a single documentation for a Git repository.

 */
class TerDocumentJob implements \TYPO3\Queue\Job\JobInterface {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\Ter\PackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Package\PackageManagerInterface
	 */
	protected $packageManager;

	/**
	 * The document
	 *
	 * @var \TYPO3\Docs\Domain\Model\Document
	 */
	protected $document;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

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
	 * Where to find the directory containing the make
	 *
	 * @var string
	 */
	protected $makeDirectory;

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
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Directory
	 */
	protected $directoryFinder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\File
	 */
	protected $fileFinder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Service\Sync\JobService
	 */
	protected $syncJobService;

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();
	}

	/**
	 * Constructor
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 */
	public function __construct(\TYPO3\Docs\Domain\Model\Document $document) {
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

		// @todo when extension will contain docs in directory "Documentation"
		//$this->temporaryDirectory = $this->directoryFinder::getTemporary($this->document);
		$this->makeDirectory = $this->outputDirectory . '/t3pdb/Documentation/_make';
		$this->warningFile = $this->makeDirectory . "/Warnings.txt";
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
	 * Execute the job
	 * A job should finish itself after successful execution using the queue methods.
	 *
	 * @param \TYPO3\Queue\QueueInterface $queue
	 * @param \TYPO3\Queue\Message $message The original message
	 * @return boolean TRUE if the job was executed successfully and the message should be finished
	 */
	public function execute(\TYPO3\Queue\QueueInterface $queue, \TYPO3\Queue\Message $message) {
		$this->systemLogger->log("Ter: processing {$this->document->getPackageKey()} {$this->document->getVersion()}");
		$this->initialize();
		$this->prepareSource();

		// @todo implement the chain of responsibility pattern for this row of "if"
		if (file_exists($this->inputDirectory . '/Documentation/Index.rst')) {
			$this->status = \TYPO3\Docs\Utility\StatusMessage::SYNC;
			// @todo call Martin's script 1
			$this->systemLogger->log('not implemented!!! - Ter: rendering completed for document ' . $this->document->getUri(), LOG_INFO);

		} elseif (file_exists($this->inputDirectory . '/doc/manual.sxw')) {

			$this->renderLegacyDocumentAndLog();

			// Create a new job for synchronizing files
			$job = $this->syncJobService->create($this->document->getPackageKey());
			$this->syncJobService->queue($job);

		} else {
			$this->status = \TYPO3\Docs\Utility\StatusMessage::NOT_FOUND;
			\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->outputDirectory); # Clean up file structure
			$this->systemLogger->log('Ter: nothing to render for document ' . $this->document->getUri(), LOG_INFO);
		}

		$this->persistAndCleanUp();
		$this->systemLogger->log("-----------------------------------------------");
		return TRUE;
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
			\TYPO3\Docs\Utility\Console::run($this->getSxw2HtmlCommand());

			$this->overwriteMake(); // should be temporary
			\TYPO3\Docs\Utility\Console::run($this->getMakeCleanCommand());
			\TYPO3\Docs\Utility\Console::run($this->getMakeHtmlCommand());

			$this->systemLogger->log('Ter: rendering finished: ', LOG_INFO);
			$this->systemLogger->log('     -> rendered document located at ' . $this->outputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> source files located at ' . $this->inputDirectory, LOG_INFO);
			$this->systemLogger->log('     -> temporary files located at ' . $this->makeDirectory, LOG_INFO);
			$this->sendAlertToMaintainers();

			$this->status = \TYPO3\Docs\Utility\StatusMessage::SYNC;
		} else {
			\TYPO3\Docs\Utility\Console::output($this->getSxw2HtmlCommand());
			\TYPO3\Docs\Utility\Console::output($this->getMakeCleanCommand());
			\TYPO3\Docs\Utility\Console::output($this->getMakeHtmlCommand());
		}
	}

	/**
	 * Should be temporary measure prior to update Make file in package RestTools
	 *
	 * @return void
	 */
	protected function overwriteMake() {
		$view = new \TYPO3\Fluid\View\StandaloneView();
		$view->setTemplatePathAndFilename('resource://TYPO3.Docs/Private/Templates/Build/Makefile.legacy.fluid');

		$makeFile = $this->makeDirectory . '/Makefile';
		file_put_contents($makeFile, $view->render());
	}

	/**
	 * Send a possible alert to the maintainer if an Exception was found
	 *
	 * @return void
	 */
	protected function sendAlertToMaintainers() {
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

	/**
	 * Persist and clean up environment
	 *
	 * @return void
	 */
	protected function persistAndCleanUp() {

		// Persist only if not dry-run mode
		if (!$this->dryRun) {
			$this->documentRepository->resetUriAlias($this->document->getPackageKey());
			$this->document->setStatus($this->status);
			$this->documentRepository->update($this->document);

			// Persist "manually"
			$this->persistenceManager->persistAll();

		} elseif ($this->document->getStatus() == \TYPO3\Docs\Utility\StatusMessage::RENDER) { // TRUE when flag "dry-run" is set and new document
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
		$package = $this->packageManager->getPackage('RestTools')->getPackagePath();
		$command = sprintf('cd %s; /usr/bin/python %s %s/doc/manual.sxw %s 2>&1 >> %sLogs/sxw2html.log',
			$package . $this->settings['sxw2htmlPath'],
			$this->settings['sxw2html'],
			FLOW3_PATH_ROOT . $this->inputDirectory,
			FLOW3_PATH_ROOT . $this->outputDirectory,
			FLOW3_PATH_DATA);

		return $command;
	}

	/**
	 * Generate the Make clean command that will clean the docs
	 *
	 * @return string the command
	 */
	protected function getMakeCleanCommand() {
		$command = sprintf('cd %s%s; make clean --quiet',
			FLOW3_PATH_ROOT,
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
		$command = sprintf('cd %s%s; make html --quiet 2> %s%s',
			FLOW3_PATH_ROOT,
			$this->makeDirectory,
			FLOW3_PATH_ROOT,
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