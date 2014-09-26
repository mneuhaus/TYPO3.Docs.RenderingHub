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

/**
 * Base class to render a single documentation for a Git repository.
 *
 */
abstract class AbstractDocumentJob implements \TYPO3\Jobqueue\Common\Job\JobInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * The document
	 *
	 * @var Document
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
	 * @param Document $document
	 */
	public function __construct(Document $document) {
		$this->document = $document;
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
		Console::$dryRun = $this->dryRun;
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
}
