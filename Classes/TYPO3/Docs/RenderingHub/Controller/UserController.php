<?php
namespace TYPO3\Docs\RenderingHub\Controller;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Docs\RenderingHub\Domain\Model\User;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Error\Message;
use TYPO3\Flow\Security\AccountFactory;

/**
 * Standard controller for the TYPO3.Docs package
 *
 * @Flow\Scope("singleton")
 */
class UserController extends \TYPO3\Flow\Mvc\Controller\ActionController {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Domain\Repository\UserRepository
	 */
	protected $userRepository;

	/**
	 * @Flow\Inject
	 * @var AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @var \TYPO3\Flow\Security\Cryptography\HashService
	 * @Flow\Inject
	 */
	protected $hashService;

	/**
	 * Index action
	 *
	 * @return void
	 */
	public function indexAction() {
		$this->view->assign('users', $this->userRepository->findAll());
	}

	/**
	 * @param User $user
	 * @return void
	 */
	public function newAction(User $user = NULL) {
		$this->view->assign('user', $user);
	}

	/**
	 * @param User $user
	 * @param string $username
	 * @param string $password
	 * @param string $confirmation
	 * @return void
	 */
	public function createAction(User $user, $username, $password, $confirmation) {
		if ($password !== NULL && !empty($password)) {
			if ($password !== $confirmation) {
				$this->addFlashMessage('Password and Confirmation must be the same!', '', Message::SEVERITY_ERROR);
				$this->userRepository->update($user);
				$this->redirect('edit', NULL, NULL, array('user' => $user));
			}

			$roles = array('TYPO3.Docs.RenderingHub:Administrator');
			$account = $this->accountFactory->createAccountWithPassword($username, $password, $roles, 'DocsRenderingHubProvider');
			$account->setParty($user);
			$this->persistenceManager->add($account);
		}
		$this->userRepository->add($user);
		$this->addFlashMessage('Created user successfully!');
		$this->redirect('index');
	}

	/**
	 * @param User $user
	 * @return void
	 */
	public function editAction(User $user) {
		$this->view->assign('user', $user);
	}

	/**
	 * @param User $user
	 * @param string $password
	 * @param string $confirmation
	 * @return void
	 */
	public function updateAction(User $user, $password = NULL, $confirmation = NULL) {
		if ($this->updateUser($user, $password, $confirmation) === FALSE) {
			return;
		}
		$this->addFlashMessage('Updated user successfully!');
		$this->redirect('index');
	}

	/**
	 * @param User $user
	 * @return void
	 */
	public function editProfileAction(User $user) {
		$this->view->assign('user', $user);
	}

	/**
	 * @param User $user
	 * @param string $password
	 * @param string $confirmation
	 * @return void
	 */
	public function updateProfileAction(User $user, $password = NULL, $confirmation = NULL) {
		if ($this->updateUser($user, $password, $confirmation) === FALSE) {
			return;
		}
		$this->addFlashMessage('Updated user successfully!');
		$this->redirect('editProfile', NULL, NULL, array('user' => $user));
	}

	/**
	 * @param User $user
	 * @param string $password
	 * @param string $confirmation
	 * @return void
	 */
	public function updateUser(User $user, $password = NULL, $confirmation = NULL) {
		if ($password !== NULL && !empty($password)) {
			if ($password !== $confirmation) {
				$this->addFlashMessage('Password and Confirmation must be the same!', '', Message::SEVERITY_ERROR);
				$this->userRepository->update($user);
				$this->redirect('edit', NULL, NULL, array('user' => $user));
				return FALSE;
			}

			$credentialsSource = $this->hashService->hashPassword($password, "default");
			$account = $user->getMainAccount();
			$account->setCredentialsSource($credentialsSource, "default");
			$this->persistenceManager->update($account);
		}
		$this->userRepository->update($user);

		return TRUE;
	}

	/**
	 * @param User $user
	 * @return void
	 */
	public function confirmAction(User $user) {
		$this->view->assign('user', $user);
	}

	/**
	 * @param User $user
	 * @return void
	 */
	public function deleteAction(User $user) {
		$this->userRepository->remove($user);
		$this->persistenceManager->remove($user->getMainAccount());
		$this->redirect('index');
		$this->addFlashMessage('Updated user deleted!');
	}

}

?>