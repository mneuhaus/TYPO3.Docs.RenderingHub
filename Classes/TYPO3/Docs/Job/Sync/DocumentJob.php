<?php
namespace TYPO3\Docs\Job\Sync;

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
class DocumentJob implements \TYPO3\Jobqueue\Common\Job\JobInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @var string
	 */
	protected $packageKey;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\UserLogger
	 */
	protected $userLogger;

	/**
	 * Whether command should be executed or displayed
	 *
	 * @var boolean
	 */
	protected $dryRun = FALSE;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Directory
	 */
	protected $directoryFinder;

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
	 * @param string $packageKey
	 */
	public function __construct($packageKey) {
		$this->packageKey = $packageKey;
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

		$documents = $this->documentRepository->findDocumentsWaitingToBeSynced($this->packageKey);
		if (count($documents) === 0) {
			$this->systemLogger->log("Sync: nothing to synchronize for package {$this->packageKey}");
			return TRUE;
		}

		$this->systemLogger->log("Sync: files synchronizing for package {$this->packageKey}");

		/** @var $document \TYPO3\Docs\Domain\Model\Document */
		foreach ($documents as $document) {

			// Generate
			$buildPath = $this->getBuildDocumentPath($document);
			if (file_exists($buildPath)) {

				$this->systemLogger->log("Sync: starting synchronization " . $document->getUri());
				$this->rsync($buildPath, $document->getUri());

				if ($document->getUriAlias() !== '') {

					// Get new public path for uri alias
					$this->systemLogger->log("Sync: starting synchronization for URI alias " . $document->getUri());
					$this->rsync($buildPath, $document->getUriAlias());
				}

				// Persist document with new status
				$document->setStatus(\TYPO3\Docs\Domain\Model\Document::STATUS_OK);
				$this->documentRepository->update($document);
				$this->persistenceManager->persistAll();
			} else {
				$this->systemLogger->log("Sync: path not found $buildPath. Are you in dry-run mode?");
			}

		}

		$this->systemLogger->log("-----------------------------------------------");
		return TRUE;
	}

	/**
	 * Rsync the build direction with the public
	 *
	 * @param string $publicPath
	 * @param string $buildPath
	 * @return void
	 */
	protected function rsync($buildPath, $publicPath) {

		$publicPath = $this->getPublicPath($publicPath);

		$command = $this->getAliasSyncCommand($buildPath, $publicPath);
		$this->systemLogger->log('     -> build path ' . $buildPath);
		$this->systemLogger->log('     -> public path ' . $publicPath);
		\TYPO3\Docs\Utility\Console::run($command);
	}

	/**
	 * Generate the sync command for a document
	 *
	 * @param string $buildPath
	 * @param string $publicPath
	 * @return string
	 *
	 * @deprecated will be removed in 1.2
	 */
	protected function getSyncCommand($buildPath, $publicPath) {

		$parts = explode('/', $publicPath);
		array_pop($parts);
		$publicPath = implode('/', $parts);

		$command = sprintf('rsync -aE --delete %s %s',
			$buildPath,
			$publicPath
		);

		return $command;
	}

	/**
	 * Generate the sync command for a document
	 *
	 * @param string $buildPath
	 * @param string $publicPath
	 * @return string
	 */
	protected function getAliasSyncCommand($buildPath, $publicPath) {

		// rsync behaves strangely when target directory is not the same as the source
		// Thus remove the alias uri path first which will be re-created by rsync
		\TYPO3\Flow\Utility\Files::removeDirectoryRecursively($publicPath);

		$command = sprintf('rsync -aE --delete %s/ %s',
			$buildPath,
			$publicPath
		);

		return $command;
	}

	/**
	 * Get a private path where the final document build is stored
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string
	 */
	protected function getBuildDocumentPath($document) {

		$buildPath = $this->directoryFinder->getBuild($document);
		$terBuild = $buildPath . '/t3pdb/Documentation/_make/build/html';

		// TRUE for Ter documentation
		if (is_dir($terBuild)) {
			return $terBuild;
		}

		return $buildPath;
	}

	/**
	 * Get a public path
	 *
	 * @param string $uri
	 * @param bool $createDirectory
	 * @return string
	 */
	protected function getPublicPath($uri = '', $createDirectory = TRUE) {
		$publicPath = FLOW_PATH_WEB . ltrim($uri, '/');
		if ($createDirectory) {
			\TYPO3\Flow\Utility\Files::createDirectoryRecursively($publicPath);
		}
		return $publicPath;
	}

	/**
	 * Get the identifier
	 *
	 * @return string
	 */
	public function getIdentifier() {
		return 'sync';
	}

	/**
	 * Get the label
	 *
	 * @return string
	 */
	public function getLabel() {
		return 'Sync Document job ' . $this->inputDirectory;
	}

	/**
	 * @param boolean $dryRun
	 */
	public function setDryRun($dryRun) {
		$this->dryRun = $dryRun;
	}
}
?>