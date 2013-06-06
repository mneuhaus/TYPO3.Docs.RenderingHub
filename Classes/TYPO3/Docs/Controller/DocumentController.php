<?php
namespace TYPO3\Docs\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Standard controller for the TYPO3.Docs package
 *
 * @Flow\Scope("singleton")
 */
class DocumentController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * Create a new document
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document the document to be added
	 * @return void
	 */
	public function createAction(\TYPO3\Docs\Domain\Model\Document $document) {
		$this->documentRepository->add($document);
	}
}

?>