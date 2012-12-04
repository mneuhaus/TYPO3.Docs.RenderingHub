<?php
namespace TYPO3\Docs\Finder\Directory;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class for resolving directory path of a git package
 *
 * @FLOW3\Scope("singleton")
 */
class GitPackage implements \TYPO3\Docs\Finder\Directory\FinderInterface {

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
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();
	}

	/**
	 * Returns a directory where to find the source of the package.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string the path
	 */
	public function getSource(\TYPO3\Docs\Domain\Model\Document $document) {
		$uriParts = \TYPO3\FLOW3\Utility\Arrays::trimExplode('/', $document->getUri());
		array_pop($uriParts);

		$repositoryPath = $this->settings['gitSourceDir'] . '/' . implode('/', $uriParts);
		\TYPO3\FLOW3\Utility\Files::createDirectoryRecursively($repositoryPath);
		return $repositoryPath;
	}

	/**
	 * Returns the directory where to find the documentation rendered.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getBuild(\TYPO3\Docs\Domain\Model\Document $document) {
		$directoryPath = sprintf('%s/%s', $this->settings['buildDir'], $document->getUri());
		\TYPO3\FLOW3\Utility\Files::createDirectoryRecursively($directoryPath);
		return $directoryPath;
	}

	/**
	 * Returns the directory where to find temporary data.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getTemporary(\TYPO3\Docs\Domain\Model\Document $document) {
		$directoryPath = sprintf('%s/%s-%s', $this->settings['temporaryDir'], $document->getPackageKey(), $document->getVersion());
		\TYPO3\FLOW3\Utility\Files::createDirectoryRecursively($directoryPath);
		return $directoryPath;
	}
}
?>