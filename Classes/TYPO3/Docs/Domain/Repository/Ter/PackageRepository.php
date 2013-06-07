<?php

namespace TYPO3\Docs\Domain\Repository\Ter;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A repository for Ter packages
 *
 * @Flow\Scope("singleton")
 */
class PackageRepository extends \TYPO3\Docs\Domain\Repository\AbstractRepository {

	const ERRORCODE_ERRORWHILEREADINGT3XFILE = 'can-not-read-t3x';

	const ERRORCODE_T3XARCHIVECORRUPTED = 't3x-archive-corrupted';

	const ERRORCODE_ERRORWHILEUNCOMPRESSINGT3XFILE = 'uncompressing-t3x-error';

	const ERRORCODE_CORRUPTEDT3XSTRUCTURENOFILESFOUND = 'corrupted-file-structure';

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @var string
	 */
	protected $repositoryType = 'ter';

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Service\DataSource\TerService
	 */
	protected $dataSource;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\PackageRepository
	 */
	protected $packageRepository;

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
	 * Find a bunch of packages given a package key and a version number.
	 *
	 * @param string $packageKey the package name
	 * @param string $version the version number of the package to be found
	 * @return \TYPO3\Docs\Domain\Model\Package[]
	 */
	public function findByPackageKey($packageKey, $version = '') {
		$packages = array();

		foreach ($this->findAll() as $package) {

			if ($package->getTitle() === $packageKey) {

				if (empty($version)) {
					$packages[] = $package;
				} elseif($package->getVersion() === $version) {
					$packages[] = $package;
				}
			}
		}
		return $packages;
	}

	/**
	 * Retrieve a bunch of Ter packages.
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

			$this->systemLogger->log('Ter: importing new set of packages, it will take a while...', LOG_INFO);

			// First remove all Ter packages before re-inserting them
			$this->packageRepository->deleteByRepositoryType($this->repositoryType);

			// Then re-save the new set of data
			/** @var $entry \SimpleXMLElement */
			foreach ($this->dataSource->get() as $entry) {
				foreach ($entry->version as $version) {

					$versionNumber = (string)$version['version'];

					// a valid version should be defined
					if (version_compare($versionNumber, '0.0.0', '>')) {

						// Normally should be of type \TYPO3\Docs\Domain\Model\Package
						// But for performance reason use an array
						$data = array();
						$data['packageKey'] = (string)$entry['extensionkey'];
						$data['title'] = (string)$version->title;
						$data['abstract'] = (string)$version->description;
						$data['repositoryType'] = $this->repositoryType;
						$data['product'] = 'TYPO3 CMS';
						$data['locale'] = 'en_US';
						$data['type'] = 'manual';
						$data['version'] = $versionNumber;

						$this->packageRepository->add($data);
					}
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
	 * Fetch a t3x file from typo3.org. The file will be downloaded only if missing on the local file system
	 *
	 * @param string $packageLocalFile the package file
	 * @param string $packageRemoteFile the remote file URL
	 * @return mixed FALSE if operation fails, TRUE if the file already exists, int if file is successfully written
	 */
	public function fetch($packageLocalFile, $packageRemoteFile) {

		$result = TRUE;

		// download the t3x archive if not already present in the file structure
		if (!file_exists($packageLocalFile)) {

			// Fetch file and put it on the file system
			$data = file_get_contents($packageRemoteFile);
			$result = file_put_contents($packageLocalFile, $data);
			if (!$result) {
				$this->systemLogger->log('Ter: warning could not write or download "' . $packageRemoteFile . '"', LOG_WARNING);
			}
		}
		return $result;
	}

	/**
	 * Unpacks the T3X file of the given extension version and extracts the file specified
	 * in $sourceName. If the operation fails, FALSE is returned.
	 *
	 * @throws \RuntimeException
	 * @throws \TYPO3\Docs\Exception\MissingFileException
	 * @param string $packageLocalFile the package file with the full path
	 * @param string $targetDirectory the package directory where to extract data
	 * @return boolean if anything has been extracted, negative int value if an error occurs
	 */
	public function extract($packageLocalFile, $targetDirectory) {

		// computes the t3x file
		if (!is_file($packageLocalFile)) {
			throw new \TYPO3\Docs\Exception\MissingFileException('Exception thrown #1300111630: file does not exist "' . $packageLocalFile . '"', 1300111630);
		}

		$t3xFileRaw = file_get_contents($packageLocalFile);
		if ($t3xFileRaw === FALSE) {
			$this->systemLogger->log('Ter: error while reading t3x file "' . $packageLocalFile . '"', LOG_WARNING);
			return self::ERRORCODE_ERRORWHILEREADINGT3XFILE;
		}

		list ($md5Hash, $compressionFlag, $dataRaw) = preg_split('/:/is', $t3xFileRaw, 3);
		unset($t3xFileRaw);

		$dataUncompressed = gzuncompress($dataRaw);

		if ($md5Hash !== md5($dataUncompressed)) {
			$this->systemLogger->log('Ter: T3X archive is corrupted, MD5 hash didn\'t match for file "' . $packageLocalFile . '"', LOG_WARNING);
			return self::ERRORCODE_T3XARCHIVECORRUPTED;
		}
		unset($dataRaw);

		$t3xArr = unserialize($dataUncompressed);
		if (!is_array($t3xArr)) {
			$this->systemLogger->log('Ter: ERROR while uncompressing t3x file "' . $packageLocalFile . '"', LOG_WARNING);
			return self::ERRORCODE_ERRORWHILEUNCOMPRESSINGT3XFILE;
		}
		if (!is_array($t3xArr['FILES'])) {
			$this->systemLogger->log('Ter: ERROR: Corrupted t3x structure - no files found "' . $packageLocalFile . '"', LOG_WARNING);
			return self::ERRORCODE_CORRUPTEDT3XSTRUCTURENOFILESFOUND;
		}

		// Extract content related to the documentation:
		// * doc/manual.sxw
		// * Documentation/*
		if (! empty($t3xArr['FILES'])) {
			foreach ($t3xArr['FILES'] as $file) {

				if (preg_match('/^Documentation\/|^doc\/manual.sxw/is', $file['name'])) {
					$directory = $targetDirectory . '/' . dirname($file['name']);
					\TYPO3\Flow\Utility\Files::createDirectoryRecursively($directory);

					$fileFullPath = $targetDirectory . '/' . $file['name'];
					\TYPO3\Docs\Utility\Files::write($fileFullPath, $file['content']);
				}
			}
		}

		return TRUE;
	}
}

?>
