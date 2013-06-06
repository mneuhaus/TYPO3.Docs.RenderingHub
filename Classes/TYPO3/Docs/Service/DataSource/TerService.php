<?php
namespace TYPO3\Docs\Service\DataSource;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Ter data source
 *
 * @Flow\Scope("singleton")
 */
class TerService implements \TYPO3\Docs\Service\DataSource\ServiceInterface {

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
	 * @var \TYPO3\Docs\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();
	}

	/**
	 * Returns a bunch of data coming from the data-source
	 * Ter data-source is serialized in XML.
	 * It will raise an exception if the data-source is not found.
	 *
	 * @throws \TYPO3\Docs\Exception\XmlParsingException
	 * @throws \TYPO3\Docs\Exception\MissingDataSourceException
	 * @return \SimpleXMLElement
	 */
	public function get() {

		// Make sure the file exists
		if (!is_file($this->settings['terDatasource'])) {
			throw new \TYPO3\Docs\Exception\MissingDataSourceException('There is no data source. File not found ' . $this->settings['terDatasource'], 1345549138);
		}

		// Transfer data from extensions.xml.gz to database:
		$unzippedExtensionsXML = implode('', @gzfile($this->settings['terDatasource']));

		/** @var $xml \SimpleXMLElement */
		$entries = new \SimpleXMLElement($unzippedExtensionsXML);
		if (!is_object($entries)) {
			throw new \TYPO3\Docs\Exception\XmlParsingException('Error while parsing ' . $this->settings['terDatasource'], 1300783708);
		}

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
			$this->systemLogger->log('Ter: data source has been updated with success', LOG_INFO);
		}
		return $isCacheObsolete;
	}

	/**
	 * Check whether the data source should be updated. This will happen
	 * when extension data source on typo3.org is more recent that the local version.
	 *
	 * @return boolean
	 */
	protected function isCacheObsolete() {
		$localUnixTime = \TYPO3\Docs\Utility\Files::getModificationTime($this->settings['terDatasource']);
		$remoteUnixTime = \TYPO3\Docs\Utility\Files::getRemoteModificationTime($this->settings['terDatasourceRemote']);
		return $remoteUnixTime > $localUnixTime;
	}

	/**
	 * Update from typo3.org the latest version of the data source of extensions (AKA extensions.xml.gz).
	 *
	 * @return void
	 */
	public function write() {
		\TYPO3\Flow\Utility\Files::createDirectoryRecursively(dirname($this->settings['terDatasource']));
		$content = \TYPO3\Flow\Utility\Files::getFileContents($this->settings['terDatasourceRemote']);
		\TYPO3\Docs\Utility\Files::write($this->settings['terDatasource'], $content);
	}
}

?>