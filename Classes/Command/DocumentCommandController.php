<?php
namespace TYPO3\Docs\Command;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Document rendering command controller
 * to be used as a basis for the documentation rendering by the doc team
 *
 * @FLOW3\Scope("singleton")
 */
class DocumentCommandController extends \TYPO3\FLOW3\Cli\CommandController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\TerPackageRepository
	 */
	protected $terPackageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\GitPackageRepository
	 */
	protected $gitPackageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @var array
	 */
	protected $sources = array(
		'ter' => 'packages from TER',
		'git' => 'packages from git.typo3.org',
	);

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Inject the settings
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings['defaultConfiguration'];
	}


	/**
	 * Render a specific document from a given package. A repository type can also be given as parameter: "ter", "git".
	 * If no repository type is given, then render from all repository types.
	 * The command also accept a "version" parameter, for rendering a specific version. Unless this parameter is transmitted,
	 * all versions will be rendered.
	 *
	 * Usage:
	 * ./flow3 document:render [REPOSITORY_TYPE]
	 *
	 * Where REPOSITORY_TYPE is "ter" "git" "svn "
	 *
	 * Example:
	 * ./flow3 document:render --package news ter
	 * ./flow3 document:render --package news --version 1.0.0 ter
	 *
	 * @param string $package the package name to be rendered
	 * @param string $version the version number
	 * @param string $format comma separated list of format: html,pdf,...
	 * @param boolean $dryRun tell whether to set the dry - run flag
	 * @return void
	 */
	public function renderCommand($package, $version = '', $format = 'html', $dryRun = FALSE) {

		$sources = $this->getSourcesArguments();

		foreach ($sources as $source) {
			$className = '\TYPO3\Docs\Controller\\' . ucfirst($source) . 'DocumentController';

			/** @var $controller \TYPO3\Docs\Controller\InterfaceDocumentController */
			$controller = new $className();
			$controller->renderAction($package, $version, $format, $dryRun);
		}
	}

	/**
	 * Render all documents from all packages. A repository type can also be given as parameter: "ter", "git".
	 * If no repository type is given, then render from all repository types.
	 *
	 * Usage:
	 * ./flow3 document:renderall [REPOSITORY_TYPE]
	 *
	 * Where REPOSITORY_TYPE is "ter" "git" "svn "
	 *
	 * Example:
	 * ./flow3 document:renderall git
	 * ./flow3 document:renderall ter
	 * ./flow3 document:renderall ter --force
	 * ./flow3 document:renderall ter --limit 10
	 *
	 * @param int $limit to prevent exceeding the memory
	 * @param string $format a comma separated list of formats (html, ebook, pdf, ...)
	 * @param boolean $force tell whether to skip message validation
	 * @param boolean $dryRun tell whether to set the dry - run flag
	 * @return void
	 */
	public function renderAllCommand($limit = 0, $format = 'html', $force = FALSE, $dryRun = FALSE) {

		$sources = $this->getSourcesArguments();

		if ($limit == 0 && !$force) {
			$message = $this->getRenderMessage($sources);
			if (!\TYPO3\Docs\Utility\Console::askUserValidation($message)) {
				$this->quit();
			}
		}

		foreach ($sources as $source) {
			$className = '\TYPO3\Docs\Controller\\' . ucfirst($source) . 'DocumentController';

			/** @var $controller \TYPO3\Docs\Controller\InterfaceDocumentController */
			$controller = new $className();
			$controller->renderAllAction($limit, $force, $format, $dryRun);
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
	 * Remove a generated documents given a package name. This will erase files + database entries.
	 * Use option "force" to skip the message
	 *
	 * @param string $package a package name
	 * @param boolean $force skip user validation
	 * @return void
	 */
	public function flushCommand($package, $force = FALSE) {

		$action = '';
		$action .= '- remove all documents for package "' . $package . '" from the database' . PHP_EOL;
		#$action .= '- remove source and build files for package "' . $package . '" from the database' . PHP_EOL;

		$message = <<< EOF
You are going to perform the following actions:

$action
Aye you sure of that?\nPress y or n:
EOF;

		if (\TYPO3\Docs\Utility\Console::askUserValidation($message, $force)) {
			$documents = $this->documentRepository->findByPackageKey($package);
			foreach ($documents as $document) {
				$this->documentRepository->remove($document);
			}
			# @todo
			#if (is_dir($this->settings['sourceDir'] . '/.../)) {
			#	\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->settings['sourceDir']);
			#}

			#if (is_dir($this->settings['buildDir'])) {
			#	\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->settings['buildDir']);
			#}
		}

	}

	/**
	 * Remove all generated documents. This will erase files + database entries.
	 * Use option "force" to skip the message
	 *
	 * @param boolean $databaseOnly flush only the database
	 * @param boolean $force skip user validation
	 * @return void
	 */
	public function flushAllCommand($databaseOnly = FALSE, $force = FALSE) {

		$action = '';
		$action .= '- remove all documents from the database' . PHP_EOL;

		if (! $databaseOnly) {
			$action .= '- remove all source files form ' . $this->settings['sourceDir'] . PHP_EOL;
			$action .= '- remove all builds from ' . $this->settings['buildDir'] . PHP_EOL;
		}

		$message = <<< EOF
You are going to perform the following actions:

$action
Aye you sure of that?\nPress y or n:
EOF;

		if (\TYPO3\Docs\Utility\Console::askUserValidation($message, $force)) {
			$this->documentRepository->removeAll();

			if (!$databaseOnly && is_dir($this->settings['sourceDir'])) {
				\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->settings['sourceDir']);
			}

			if (!$databaseOnly && is_dir($this->settings['buildDir'])) {
				\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->settings['buildDir']);
			}

			if (!$databaseOnly && is_dir($this->settings['temporaryDir'])) {
				\TYPO3\FLOW3\Utility\Files::removeDirectoryRecursively($this->settings['temporaryDir']);
			}
		}
	}

	/**
	 * List sources of packages
	 *
	 * @return void
	 */
	public function listSourcesCommand() {
		$this->outputLine('Source   Description');
		$this->outputLine('------   -----------');
		foreach ($this->sources as $source => $description) {
			$this->outputLine('%s      %s', array($source, $description));
		}
	}

	/**
	 * Validate and returns arguments in the range of $this->sources.
	 *
	 * @return array
	 */
	protected function getSourcesArguments() {
		// Check validity of exceeding arguments and stop if an un-registered value is encountered
		$arguments = $this->request->getExceedingArguments();
		foreach ($arguments as $argument) {
			if (!in_array($argument, array_keys($this->sources))) {
				$this->outputLine('Not recognized argument %s', array($argument));
				$this->outputLine('Possibles arguments: %s', array(implode(', ', $this->sources)));
				$this->quit();
			}
		}

		// If no exceeding argument has been passed, then fill them manually
		if (empty($arguments)) {
			$arguments = array_keys($this->sources);
		}

		return $arguments;
	}

	/**
	 * Generates and returns a message to be displayed on the console regarding the fetch command.
	 *
	 * @param array $sources of synchronization.
	 * @return string
	 */
	protected function getRenderMessage(array $sources) {

		$action = '';
		if (in_array('ter', $sources)) {
			// Generate figures for the TER
			$numberOfTerPackagesWithVersions = $this->terPackageRepository->countAllWithVersions();
			$numberOfTerDocument = $this->documentRepository->countByRepositoryType('ter');
			$numberOfTerDocumentToProcess = $numberOfTerPackagesWithVersions - $numberOfTerDocument;
			$action .= '- render ' . $numberOfTerDocumentToProcess . ' new document(s) from the TER' . PHP_EOL;
		}

		if (in_array('git', $sources)) {
			// Generate figures for the TER
			$numberOfGitPackagesWithVersions = $this->gitPackageRepository->countAllWithVersions();
			$numberOfGitDocument = $this->documentRepository->countByRepositoryType('git');
			$numberOfGitDocumentToProcess = $numberOfGitPackagesWithVersions - $numberOfGitDocument;
			$action .= '- insert ' . $numberOfGitDocumentToProcess . ' new document(s) from git.typo3.org' . PHP_EOL;
		}

		return <<< EOF
You are going to perform the following actions:

$action

Note: consider adding a limit if the number of items is too big to avoid the system to run out of memory.

.flow3 document:renderall ter --limit 100

Aye you sure of that?\nPress y or n:
EOF;
	}
}

?>