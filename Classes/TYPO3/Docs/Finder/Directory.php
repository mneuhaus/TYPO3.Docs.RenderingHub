<?php
namespace TYPO3\Docs\Finder;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving directory path of a document
 *
 * @Flow\Scope("singleton")
 */
class Directory implements \TYPO3\Docs\Finder\Directory\FinderInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Directory\GitPackage
	 */
	protected $gitPackageFinder;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Directory\TerPackage
	 */
	protected $terPackageFinder;

	/**
	 * Returns a directory where to find the source of the package.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string the path
	 */
	public function getSource(\TYPO3\Docs\Domain\Model\Document $document) {
		return $this->getFinder($document)->getSource($document);
	}

	/**
	 * Returns the directory where to find the documentation rendered.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getBuild(\TYPO3\Docs\Domain\Model\Document $document) {
		return $this->getFinder($document)->getBuild($document);
	}

	/**
	 * Returns the directory where to find temporary data.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getTemporary(\TYPO3\Docs\Domain\Model\Document $document) {
		return $this->getFinder($document)->getTemporary($document);
	}

	/**
	 * Returns the proper finder for a document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return \TYPO3\Docs\Finder\Directory\FinderInterface
	 */
	protected function getFinder($document) {
		$repositoryType = $document->getRepositoryType();
		$finderName = $repositoryType . 'PackageFinder';
		return $this->$finderName;
	}
}

?>