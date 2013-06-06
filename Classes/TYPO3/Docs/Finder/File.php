<?php
namespace TYPO3\Docs\Finder;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving directory path of a git package
 *
 * @Flow\Scope("singleton")
 */
class File implements \TYPO3\Docs\Finder\File\FinderInterface {

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
	 * Returns the remote file URL
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string path name with the file name append
	 */
	public function getRemoteFile(\TYPO3\Docs\Domain\Model\Document $document) {
		return $this->settings['terUrl'] . $document->getPackageFile();
	}

	/**
	 * Returns the full path including file name but excluding file extension of
	 * the specified extension version in the file repository.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string path name with the file name append
	 */
	public function getExtensionFileNameAndPath(\TYPO3\Docs\Domain\Model\Document $document) {
		$segments = array($this->settings['terSourceDir'], $document->getPackageFile());
		return implode('/', $segments);
	}

	/**
	 * Returns the full path including file name but excluding file extension of
	 * the specified extension version in the file repository.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getExtensionFileNameAndSubPath(\TYPO3\Docs\Domain\Model\Package $package) {
		$firstLetter = strtolower(substr($package->getPackageKey(), 0, 1));
		$secondLetter = strtolower(substr($package->getPackageKey(), 1, 1));
		$subPath = $firstLetter . '/' . $secondLetter . '/';

		return $subPath . $this->getExtensionFileName($package->getPackageKey(), $package->getVersion());
	}

	/**
	 * Returns the file name but excluding file extension of
	 * the specified extension version in the file repository.
	 *
	 * @param string $extensionKey the extension key
	 * @param string $version the version number
	 * @return string the extension name
	 */
	public function getExtensionFileName($extensionKey, $version) {
		list ($majorVersion, $minorVersion, $devVersion) = \TYPO3\Flow\Utility\Arrays::integerExplode('.', $version);
		return strtolower($extensionKey) . '_' . $majorVersion . '.' . $minorVersion . '.' . $devVersion;
	}
}

?>