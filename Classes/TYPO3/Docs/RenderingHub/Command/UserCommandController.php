<?php
namespace TYPO3\Docs\RenderingHub\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Causal.Docst3o".        *
 *                                                                        *
 *                                                                        */

use TYPO3\Docs\RenderingHub\Domain\Model\User;
use TYPO3\Docs\RenderingHub\Domain\Repository\UserRepository;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Security\AccountFactory;
use TYPO3\Flow\Security\AccountRepository;
use TYPO3\Party\Domain\Model\PersonName;

/**
 * The User Command Controller
 *
 * @Flow\Scope("singleton")
 */
class UserCommandController extends \TYPO3\Flow\Cli\CommandController {
	/**
	 * @Flow\Inject
	 * @var AccountRepository
	 */
	protected $accountRepository;

	/**
	 * @Flow\Inject
	 * @var UserRepository
	 */
	protected $userRepository;

	/**
	 * @Flow\Inject
	 * @var AccountFactory
	 */
	protected $accountFactory;

	/**
	 * @var string
	 */
	protected $authenticationProvider = 'DocsRenderingHubProvider';

	/**
	 * Create a new user
	 *
	 * This command creates a new user which has access to the backend user interface.
	 * It is recommended to user the email address as a username.
	 *
	 * @param string $username The username of the user to be created.
	 * @param string $password Password of the user to be created
	 * @param string $firstName First name of the user to be created
	 * @param string $lastName Last name of the user to be created
	 * @param string $roles Roles to add to the user
	 * @return void
	 */
	public function createCommand($username, $password, $firstName, $lastName, $roles = NULL) {
		$account = $this->accountRepository->findByAccountIdentifierAndAuthenticationProviderName($username, $this->authenticationProvider);
		if ($account instanceof \TYPO3\Flow\Security\Account) {
			$this->outputLine('The username "%s" is already in use', array($username));
			$this->quit(1);
		}

		$user = new User();
		$name = new PersonName('', $firstName, '', $lastName, '', $username);
		$user->setName($name);

		$this->userRepository->add($user);

		if ($roles !== NULL) {
			$roles = explode(',', $roles);
		} else {
			$roles = array('TYPO3.Docs.RenderingHub:Administrator');
		}
		$account = $this->accountFactory->createAccountWithPassword($username, $password, $roles, $this->authenticationProvider);
		$account->setParty($user);
		$this->accountRepository->add($account);

		$this->outputLine('Created user "%s".', array($username));
	}

}
