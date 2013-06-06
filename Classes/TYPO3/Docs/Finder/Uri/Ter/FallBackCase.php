<?php
namespace TYPO3\Docs\Finder\Uri\Ter;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Uri coming from Ter packages
 */
class FallBackCase extends \TYPO3\Docs\Finder\Uri\Ter\AbstractCase {

	/**
	 * @param \TYPO3\Docs\Finder\Uri\Ter\AbstractCase $nextCase
	 * @return void
	 */
	public function setSuccessor(\TYPO3\Docs\Finder\Uri\Ter\AbstractCase $nextCase) {
		$this->successor = $nextCase;
	}

	/**
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string
	 */
	public function handle($package) {
		return sprintf('/typo3cms/extensions/%s/%s', $package->getPackageKey(), $package->getVersion());
	}
}

?>