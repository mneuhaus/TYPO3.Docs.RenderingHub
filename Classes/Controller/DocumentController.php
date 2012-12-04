<?php
namespace TYPO3\Docs\Controller;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Standard controller for the TYPO3.Docs package
 *
 * @FLOW3\Scope("singleton")
 */
class DocumentController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {

	/**
	 * @FLOW3\Inject
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