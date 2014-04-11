<?php
namespace TYPO3\Docs\RenderingHub\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Docs\RenderingHub\Utility\Console;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Utility\Files;

/**
 * Document rendering command controller.
 *
 * @Flow\Scope("singleton")
 */
class DocumentCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var array
	 */
	protected $repositoryTypes = array(
		'ter',
		'git',
	);

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Directory
	 */
	protected $directoryFinder;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Utility\LockFile
	 */
	protected $lockFile;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * @var array
	 */
	protected $settings;

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
	 * Render a specific document from a given package key.
	 *
	 * A repository type can also be given as parameter: "ter", "git". If no repository type is given,
	 * then render from all repository types.
	 *
	 * The command also accept a "version" parameter, for rendering a specific version. Unless this parameter is
	 * given, all versions of the package will be rendered e.g 1.0.0, 1.1.0, master, ....
	 *
	 * @param string $package the package name to be rendered
	 * @param string $version the version number
	 * @param string $format comma separated list of format: html,pdf,...
	 * @param boolean $dryRun tell whether to set the dry - run flag
	 * @return void
	 */
	public function importCommand($package, $version = '', $format = 'html', $dryRun = FALSE) {

		$this->runTimeSettings->setFormats($format);
		$this->runTimeSettings->setDryRun($dryRun);

		$repositoryTypes = $this->getRepositoryTypesArguments();

		foreach ($repositoryTypes as $repositoryType) {
			$this->documentRepository->importByRepositoryType($repositoryType, $package, $version);
		}
	}

	/**
	 * Import and render all documents from all repository types.
	 *
	 * A repository type can also be given as parameter: "ter", "git" to limit the scope.
	 *
	 * @param integer $limit to prevent exceeding the memory
	 * @param string $format a comma separated list of formats (html, ebook, pdf, ...)
	 * @param boolean $force tell whether to skip message validation
	 * @param boolean $dryRun tell whether to set the dry - run flag
	 * @return void
	 */
	public function importAllCommand($limit = 0, $format = 'html', $force = FALSE, $dryRun = FALSE) {

		if (! $this->lockFile->exists() || $force) {

			$this->runTimeSettings->setFormats($format);
			$this->runTimeSettings->setForce($force);
			$this->runTimeSettings->setDryRun($dryRun);
			$this->runTimeSettings->setLimit($limit);

				// Action can take a while, adding lock file avoiding concurrent operations
			$this->lockFile->create();

			$repositoryTypes = $this->getRepositoryTypesArguments();

			if ($limit === 0 && !$force) {
				$message = $this->getImportAllMessage($repositoryTypes);

				if ($message && !Console::askUserValidation($message)) {
					$this->lockFile->remove();
					$this->quit();
				}
			}

			foreach ($repositoryTypes as $repositoryType) {
				$this->documentRepository->importAllByRepositoryType($repositoryType);
			}
				// remove lock file as a final step
			$this->lockFile->remove();
		} else {
			$this->outputLine('Lock file found, I can not proceed any further. Use option --force to pass me over!');
		}
	}

	/**
	 * Update rendering for all documents.
	 *
	 * @param integer $limit to prevent exceeding the memory
	 * @param string $format a comma separated list of formats (html, ebook, pdf, ...)
	 * @param boolean $force tell whether to skip message validation
	 * @param boolean $dryRun tell whether to set the dry - run flag
	 * @return void
	 */
	public function updateAllCommand($limit = 0, $format = 'html', $force = FALSE, $dryRun = FALSE) {

		if (!$this->lockFile->exists() || $force) {

			if ($limit === 0 && !$force) {
				$message = $this->getUpdateAllMessage();

				if (!Console::askUserValidation($message)) {
					$this->quit();
				}
			}

			$this->runTimeSettings->setFormats($format);
			$this->runTimeSettings->setForce($force);
			$this->runTimeSettings->setDryRun($dryRun);
			$this->runTimeSettings->setLimit($limit);

				// Action can take a while, adding lock file avoiding concurrent operations
			$this->lockFile->create();
			$this->documentRepository->updateAll();
			$this->lockFile->remove();
		} else {
			$this->outputLine('Lock file found, I can not proceed any further. Use option --force to pass me over!');
		}
	}

	/**
	 * Remove a generated documents given a package name.
	 *
	 * This will erase files + database entries. Use option "force" to skip the message.
	 *
	 * @param string $package a package name
	 * @param boolean $force skip user validation
	 * @return void
	 */
	public function removeCommand($package, $force = FALSE) {

		$message = <<< EOF
You are going to perform the following actions:

- remove all documents for package "{$package}" from the database
- remove source and build files for package "{$package}"

Aye you sure of that?
Press y or n:
EOF;

		if (Console::askUserValidation($message, $force)) {
			$documents = $this->documentRepository->findByPackageKey($package);
			foreach ($documents as $document) {
					// Collect and remove directories
				$directories = array();
				$directories[] = $this->directoryFinder->getSource($document);
				$directories[] = $this->directoryFinder->getBuild($document);
				foreach ($directories as $directory) {
					if (is_dir($directory)) {
						Files::removeDirectoryRecursively($directory);
					}
				}

					// Remove record
				$this->documentRepository->remove($document);
			}
		}
	}

	/**
	 * Flush objects related to the documentation.
	 *
	 * Use option "force" to skip the warning message
	 *
	 * @param boolean $database whether the database should be truncated
	 * @param boolean $datasource whether the data-source files should be dropped
	 * @param boolean $force skip user validation
	 * @return void
	 */
	public function flushCommand($database = FALSE, $datasource = FALSE, $force = FALSE) {

		$action = '';

		if ($database) {
			$numberOfDocuments = $this->documentRepository->countAll();
			$action .= '- remove ' . $numberOfDocuments . ' document(s) from the database' . PHP_EOL;
		}

		if ($datasource) {
			$action .= '- remove Git and Ter data-source files' . PHP_EOL;
		}

		$message = <<< EOF
You are going to perform the following actions:

$action
Aye you sure of that?
Press y or n:
EOF;

		if (empty($action)) {
			$this->outputLine('Nothing was flushed. Check out possible option --database, --datasource');
		} elseif (Console::askUserValidation($message, $force)) {

			if ($database) {
				$this->documentRepository->removeAll();
				$this->outputLine('- All Document objects have been removed');
			}

			if ($datasource) {
				$files[] = $this->settings['terDatasource'];
				$files[] = $this->settings['gitDatasource'];
				foreach ($files as $file) {
					if (file_exists($file)) {
						unlink($file);
						$this->outputLine('- Dropped file ' . $file);
					}
				}
			}
		}
	}

	/**
	 * Remove all generated documents.
	 *
	 * This will erase files + database entries. Use option "force" to skip the message
	 *
	 * @param boolean $force skip user validation
	 * @return void
	 */
	public function flushAllCommand($force = FALSE) {
		$message = $this->getFlushAllMessage();

		if (Console::askUserValidation($message, $force)) {
			$this->documentRepository->removeAll();

			if (is_dir($this->settings['sourceDir'])) {
				Files::removeDirectoryRecursively($this->settings['sourceDir']);
			}

			if (is_dir($this->settings['buildDir'])) {
				Files::removeDirectoryRecursively($this->settings['buildDir']);
			}

			if (is_dir($this->settings['temporaryDir'])) {
				Files::removeDirectoryRecursively($this->settings['temporaryDir']);
			}

			if (is_dir($this->settings['publicDir'])) {
				Files::removeDirectoryRecursively($this->settings['publicDir']);
			}
		}
	}

	/**
	 * Purge documents which are stuck in the queue.
	 *
	 * @return void
	 */
	public function purgeCommand() {

		$arrayOfStatus = array(
			'',
			\TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_RENDER,
			\TYPO3\Docs\RenderingHub\Domain\Model\Document::STATUS_SYNC,
		);

		foreach ($arrayOfStatus as $status) {
			$documents = $this->documentRepository->findByStatus($status);

			foreach ($documents as $document) {
				$this->documentRepository->remove($document);
			}
		}
	}


	/**
	 * Display logging messages with their severity - for testing / debugging purpose
	 *
	 * @return void
	 */
	public function testLoggingCommand() {
		$this->systemLogger->log('A message of severity INFO', LOG_INFO);
		$this->systemLogger->log('A message of severity WARNING -> send an email to ' . $this->settings['maintainers'], LOG_WARNING);
		$this->systemLogger->log('A message of severity ALERT -> send an email to ' . $this->settings['maintainers'], LOG_ALERT);
	}

	/**
	 * Validate and returns arguments in the range of $this->sources.
	 *
	 * @return array
	 */
	protected function getRepositoryTypesArguments() {
			// Check validity of exceeding arguments and stop if an un-registered value is encountered
		$arguments = $this->request->getExceedingArguments();
		foreach ($arguments as $argument) {
			if (!in_array($argument, $this->repositoryTypes)) {
				$this->outputLine('Not recognized argument %s', array($argument));
				$this->outputLine('Possible arguments: %s', array(implode(', ', $this->repositoryTypes)));
				$this->quit(1);
			}
		}

			// If no exceeding argument has been passed, then fill them manually
		if (empty($arguments)) {
			$arguments = $this->repositoryTypes;
		}

		return $arguments;
	}

	/**
	 * Generates and returns a message to be displayed on the console upon flushing all
	 *
	 * @return string
	 */
	protected function getFlushAllMessage() {
		return <<< EOF
You are going to perform the following actions:

- remove {$this->documentRepository->countAll()} document(s) from the database;
- remove all source files form {$this->settings['sourceDir']};
- remove all builds from {$this->settings['buildDir']};
- remove all public files from {$this->settings['publicDir']};

Aye you sure of that?
Press y or n:
EOF;
	}

	/**
	 * Generates and returns a message to be displayed on the console upon importing all
	 *
	 * @param array $repositoryTypes of synchronization.
	 * @return string
	 */
	protected function getImportAllMessage(array $repositoryTypes) {

		$action = $message = '';
		foreach ($repositoryTypes as $repositoryType) {
			$numberOfDocuments = $this->packageRepository->countPackageToProcess($repositoryType);

			// Makes sense to only display a message if more than 100 documents
			if ($numberOfDocuments > 100) {
				$action .= sprintf("- render %s new document(s) from the %s.typo3.org\n",
					$numberOfDocuments,
					$repositoryType
				);
			}
		}

		if ($action) {
			$message = <<< EOF
You are going to perform the following actions:

$action
Note: consider adding a limit if the number of items is too big to avoid the system to run out of memory.

./flow3 document:importall ter --limit 100

Aye you sure of that?
Press y or n:
EOF;
		}
		return $message;
	}

	/**
	 * Generates and returns a message to be displayed on the console upon updating
	 *
	 * @return string
	 */
	protected function getUpdateAllMessage() {

		$message = <<< EOF
You are going to perform the following actions:

- update {$this->documentRepository->countAll()} new document(s)

Note: consider adding a limit if the number of items is too big to avoid the system to run out of memory.

./flow3 document:importall ter --limit 100

Aye you sure of that?
Press y or n:
EOF;
		return $message;
	}

}

?>