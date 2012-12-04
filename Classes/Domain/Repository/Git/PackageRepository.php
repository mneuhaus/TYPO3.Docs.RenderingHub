<?php

namespace TYPO3\Docs\Domain\Repository\Git;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * A repository for Git packages
 *
 * @FLOW3\Scope("singleton")
 */
class PackageRepository extends \TYPO3\Docs\Domain\Repository\AbstractRepository {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @var string
	 */
	protected $repositoryType = 'git';

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Service\DataSource\GitService
	 */
	protected $dataSource;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\PackageRepository
	 */
	protected $packageRepository;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Repository
	 */
	protected $repositoryFinder;

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();
	}

	/**
	 * Find a bunch of packages given a package key and a version number.
	 *
	 * @param string $packageKey the package name
	 * @param string $version the version number of the package to be found
	 * @return \TYPO3\Docs\Domain\Model\Package[]
	 */
	public function findByPackageKey($packageKey, $version = '') {
		$packages = array();

		foreach ($this->findAll() as $package) {

			if ($package->getTitle() == $packageKey) {

				if (empty($version)) {
					$packages[] = $package;
				} elseif ($package->getVersion() == $version) {
					$packages[] = $package;
				}
			}
		}
		return $packages;
	}

	/**
	 * Retrieve a bunch of Git packages.
	 *
	 * @return \TYPO3\Docs\Domain\Model\Package[]
	 */
	public function findAll() {
		$this->synchronize();

		// Work around ORM for performance reason
		$dataSet = $this->packageRepository->findByRepositoryType($this->repositoryType);

		$packages = array();
		/** @var $data Array */
		foreach ($dataSet as $data) {
			$packages[] = new \TYPO3\Docs\Domain\Model\Package($data);
		}

		return $packages;
	}

	/**
	 * Synchronize the repository with the data-source.
	 *
	 * @return void
	 */
	public function synchronize() {

		if ($this->dataSource->update()) {
			$this->systemLogger->log('Git: importing new set of packages, it will take a while...', LOG_INFO);

			// First remove all Git packages before re-inserting them
			$this->packageRepository->deleteByRepositoryType($this->repositoryType);

			// Then re-save the new set of data
			foreach ($this->dataSource->get() as $entry) {
				foreach ($entry->versions as $repositoryTag => $version) {

					// Normally should be of type \TYPO3\Docs\Domain\Model\Package
					// But for performance reason use an array
					$data = array();
					$data['packageKey'] = $entry->packageKey;
					$data['title'] = 'unknown';
					$data['abstract'] = 'unknown';
					$data['repository'] = $entry->repository;
					$data['repositoryType'] = $this->repositoryType;
					$data['repositoryTag'] = $repositoryTag;
					$data['product'] = $entry->product;
					$data['locale'] = $entry->language;
					$data['type'] = $entry->type;
					$data['uri'] = $entry->uri;
					$data['version'] = $version;

					$this->packageRepository->add($data);
				}
			}
		}
	}

	/**
	 * Count the number of extensions being in the extension index file (extensions.xml.gz)
	 *
	 * @return integer
	 */
	public function countAll() {
		return count($this->findAll());
	}

	/**
	 * Checkout a particular version of a Git Repository
	 *
	 * @param string $sourceDirectory the source directory
	 * @param string $repositoryTagName the tag name of the repository to check out
	 * @return boolean TRUE if the command has return no warning / info
	 */
	public function checkout($sourceDirectory, $repositoryTagName) {

		$sourceDirectoryForLog = str_replace(FLOW3_PATH_DATA, '', $sourceDirectory);
		$this->systemLogger->log('Git: checking out branch "' . $repositoryTagName . '" in repository ' . $sourceDirectoryForLog, LOG_INFO);
		$command = "cd {$sourceDirectory}; git checkout --quiet --force " . $repositoryTagName;
		$result = \TYPO3\Docs\Utility\Console::run($command);

		return empty($result);
	}

	/**
	 * Fetch the source of the package
	 *
	 * @param string $sourceDirectory the source directory
	 * @param string $repositoryUri the URL of the repository
	 * @return boolean TRUE if the command has return no warning
	 */
	public function fetch($sourceDirectory, $repositoryUri) {

		$repositoryUrl = $this->repositoryFinder->getRepositoryUrl($repositoryUri);

		$sourceDirectoryForLog = str_replace(FLOW3_PATH_DATA, '', $sourceDirectory);
		$files = glob($sourceDirectory . '/.git/*');

		// TRUE means this is a new repository
		if (empty($files)) {
			$this->systemLogger->log('Git: cloning repository ' . $sourceDirectoryForLog, LOG_INFO);
			$command = "cd {$sourceDirectory}; git clone --quiet {$repositoryUrl} .";
			$result = \TYPO3\Docs\Utility\Console::run($command);
		} else {
			$this->systemLogger->log('Git: pulling remote ' . $repositoryUrl, LOG_INFO);
			$command = "cd {$sourceDirectory}; git checkout --quiet --force master";
			\TYPO3\Docs\Utility\Console::run($command);

			$command = "cd {$sourceDirectory}; git pull --quiet origin master";
			$result = \TYPO3\Docs\Utility\Console::run($command);
		}

		return empty($result);
	}
}

?>
