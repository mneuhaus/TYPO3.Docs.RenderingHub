<?php
namespace TYPO3\Docs\Service\DataSource;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Git data source
 *
 * @Flow\Scope("singleton")
 */
class GitService implements \TYPO3\Docs\Service\DataSource\ServiceInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\SystemLogger
	 */
	protected $systemLogger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Repository
	 */
	protected $repositoryFinder;

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
	 * Returns a bunch of data coming from the data-source.
	 * Git data-source is serialized in JSON.
	 * It will raise an exception if the data-source is not found.
	 *
	 * @throws \TYPO3\Docs\Exception\MissingDataSourceException
	 * @return array
	 */
	public function get() {

		// Make sure the file exists
		if (!is_file($this->settings['gitDatasource'])) {
			throw new \TYPO3\Docs\Exception\MissingDataSourceException('There is no data source. File not found ' . $this->settings['gitDatasource'], 1345549239);
		}

		$content = file_get_contents($this->settings['gitDatasource']);
		$entries = json_decode($content);
		return $entries;
	}

	/**
	 * Update the local data-source.
	 * Before updating from remote host check whether the cached file is obsolete.
	 * Returns TRUE if we write a new data-source file.
	 *
	 * @return boolean
	 */
	public function update() {
		$isCacheObsolete = $this->isCacheObsolete();

		// Update the datasource if needed
		if ($isCacheObsolete) {
			$this->write();
			$this->systemLogger->log('Git: data source has been updated with success', LOG_INFO);
		}
		return $isCacheObsolete;
	}

	/**
	 * Check whether the data source should be updated. This will happen when extension data source on typo3.org is more recent
	 * that the local version.
	 *
	 * @return boolean
	 */
	protected function isCacheObsolete() {
		$fileModificationTime = \TYPO3\Docs\Utility\Files::getModificationTime($this->settings['gitDatasource']);
		$cacheTimeDuration = time() - 3600;
		return $cacheTimeDuration > $fileModificationTime;
	}

	/**
	 * Update from git.typo3.org the latest version of the data source
	 *
	 * @return void
	 */
	public function write() {

		$content = file_get_contents($this->settings['gitDatasourceRemote']);

		// There is a bug in the JSON output
		// @see http://review.coreboot.org/Documentation/rest-api.html
		if (strpos($content, ")]}'") === 0) {
			$content = str_replace(")]}'", '', $content);
		}

		$packages = json_decode(trim($content));

		$cachePackages = array();
		foreach ($packages as $packageKey => $data) {

			// Makes sure to have the opening slash
			$packageKey = '/' . ltrim($packageKey, '/');

			$packagesParts = \TYPO3\Flow\Utility\Arrays::trimExplode('/', $packageKey);
			$repositoryUri = $packageKey . '.git';
			$repositoryUrl = $this->repositoryFinder->getRepositoryUrl($repositoryUri);

			// TRUE for official TYPO3 documentation
			if (preg_match('/^\/Documentation\/TYPO3/is', $packageKey)) {

				$cachePackages[] = array(
					'product' => 'TYPO3',
					'language' => 'us_US',
					'type' => strtolower($packagesParts[2]),
					'packageKey' => $packagesParts[3] . $packagesParts[2],
					'uri' => '/TYPO3/' . $packagesParts[3] . $packagesParts[2],
					'repository' => $repositoryUri,
					'versions' => $this->getRepositoryTags($repositoryUrl),
				);
			} // TRUE for TYPO3 extensions
			elseif (preg_match('/^\/TYPO3v4\/Extensions/is', $packageKey)) {

				$cachePackages[] = array(
					'product' => 'TYPO3',
					'language' => 'us_US',
					'type' => 'manual',
					'packageKey' => $packagesParts[2],
					'uri' => '/TYPO3/Extensions/' . $packagesParts[2],
					'repository' => $repositoryUri,
					'versions' => $this->getRepositoryTags($repositoryUrl, $onlyMaterBranch = TRUE),
				);
			} // TRUE for FLOW3 packages
			elseif (preg_match('/^\/FLOW3\/Packages/is', $packageKey)) {

				$cachePackages[] = array(
					'product' => 'Flow',
					'language' => 'us_US',
					'type' => 'manual',
					'packageKey' => $packagesParts[2],
					'uri' => '/FLOW3/Packages/' . $packagesParts[2],
					'repository' => $repositoryUri,
					'versions' => $this->getRepositoryTags($repositoryUrl),
				);
			}
		}

		// write cache
		\TYPO3\Docs\Utility\Files::write($this->settings['gitDatasource'], json_encode($cachePackages));
	}

	/**
	 * Query the remote Git repository and compute tags that will become version
	 *
	 * @param string $repositoryUrl
	 * @param bool $onlyMaterBranch if FALSE will not check for tags
	 * @return array
	 */
	public function getRepositoryTags($repositoryUrl, $onlyMaterBranch = FALSE) {

		// Reset variable (given by reference)
		$versions['master'] = 'master';

		if (!$onlyMaterBranch) {

			$command = sprintf('git ls-remote %s 2>&1', $repositoryUrl);
			exec($command, $tags, $errorCode);

			if ($errorCode > 0) {
				$this->systemLogger->log('Git: problem fetching tags for repository ' . $repositoryUrl, LOG_INFO);
			}

			foreach ($tags as $tag) {
				if (preg_match('/refs\/tags\/(.+)([0-9]-[0-9]-[0-9])$/is', $tag, $matches)) {
					$versions[$matches[1] . $matches[2]] = $matches[2];
				}
			}
		}
		return $versions;
	}
}

?>