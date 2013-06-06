<?php
namespace TYPO3\Docs\Finder\Uri\Git;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Uri coming from Git packages
 */
class FallBackCase extends \TYPO3\Docs\Finder\Uri\Git\AbstractCase {

	/**
	 * @param \TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase
	 * @return void
	 */
	public function setSuccessor(\TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase) {
		$this->successor = $nextCase;
	}

	/**
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string
	 */
	public function handle($package) {
		return sprintf('/%s/%s', $package->getUri(), $package->getVersion());
	}
}

?>