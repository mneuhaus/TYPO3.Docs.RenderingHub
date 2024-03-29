<?php
namespace TYPO3\Docs\RenderingHub\Controller;

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
class StandardController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function indexAction() {
		// $this->view->assign('documents', $this->documentRepository->findForHomePage());
	}

}

?>