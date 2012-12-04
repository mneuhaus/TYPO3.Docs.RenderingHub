<?php
namespace TYPO3\Docs\Finder\Uri;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class for resolving uri
 */
interface FinderInterface {

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\Domain\Model\Package $package);
}

?>