<?php
namespace TYPO3\Docs\Finder\Directory;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving directory path
 */
interface FinderInterface {

	/**
	 * Returns a directory where to find the source of the package.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string the path
	 */
	public function getSource(\TYPO3\Docs\Domain\Model\Document $document);

	/**
	 * Returns the directory where to find the documentation rendered.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getBuild(\TYPO3\Docs\Domain\Model\Document $document);

	/**
	 * Returns the directory where to find temporary data.
	 * Create the directory if it does not yet exist.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return string Full path to the document directory for the specified extension version
	 */
	public function getTemporary(\TYPO3\Docs\Domain\Model\Document $document);
}

?>