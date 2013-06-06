<?php
namespace TYPO3\Docs\Finder\Uri;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class for resolving Uri of a Ter package
 *
 * @FLOW3\Scope("singleton")
 */
class TerPackage implements \TYPO3\Docs\Finder\Uri\FinderInterface {

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\Domain\Model\Package $package) {
		$fallBackCase = new \TYPO3\Docs\Finder\Uri\Ter\FallBackCase();

		return $fallBackCase->handle($package);
	}
}
?>