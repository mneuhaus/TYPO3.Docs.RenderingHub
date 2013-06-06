<?php
namespace TYPO3\Docs\Finder\Repository;

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
	 * Returns an URL of a repository given a package type
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUrl(\TYPO3\Docs\Domain\Model\Package $package);
}

?>