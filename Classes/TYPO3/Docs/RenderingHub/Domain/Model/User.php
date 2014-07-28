<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "Causal.Docst3o".        *
 *                                                                        *
 *                                                                        */

use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;

/**
 * A User
 *
 * @Flow\Entity
 */
class User extends \TYPO3\Party\Domain\Model\Person {

	public function __toString() {
		return $this->getName()->getFullName();
	}

	public function getMainAccount() {
		return $this->accounts->current();
	}

	/**
	 * @param \Doctrine\Common\Collections\Collection $accounts
	 */
	public function setAccounts($accounts) {
		$this->accounts = $accounts;
	}
}

?>