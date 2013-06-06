<?php
namespace TYPO3\Docs\Service\Document;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Interface for Document Service
 */
interface ServiceInterface {

	/**
	 * Instantiate a new Document and add it into the Repository.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package
	 * @return \TYPO3\Docs\Domain\Model\Document
	 */
	public function create(\TYPO3\Docs\Domain\Model\Package $package);

	/**
	 * Render a Document given as input.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return void
	 */
	public function build(\TYPO3\Docs\Domain\Model\Document $document);
}

?>