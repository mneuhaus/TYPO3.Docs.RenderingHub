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
class StandardController extends \TYPO3\FLOW3\Mvc\Controller\ActionController {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Domain\Repository\DocumentRepository
	 */
	protected $documentRepository;

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('documents', $this->documentRepository->findForHomePage());
	}

	/**
	 * Render action
	 *
	 * @param string $origin a Git Origin
	 * @param string $repositoryType possible values are git, svn, ter
	 * @param string $branch
	 * @return void
	 */
	public function renderAction($origin = '', $repositoryType = 'git', $branch = 'master') {
		// Register
		#$this->gitDocumentCommandController->addCommand($origin);
	}

	/**
	 * @return void
	 */
	public function redirectAction() {
		$this->redirect('Index');
	}

}

?>