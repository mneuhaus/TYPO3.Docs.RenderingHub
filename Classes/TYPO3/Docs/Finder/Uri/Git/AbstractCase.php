<?php
namespace TYPO3\Docs\Finder\Uri\Git;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class dealing with Uri coming from Git packages
 */
abstract class AbstractCase {

	/**
	 * @param \TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase
	 * @return void
	 */
	abstract public function setSuccessor(\TYPO3\Docs\Finder\Uri\Git\AbstractCase $nextCase);

	/**
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string
	 */
	abstract public function handle($package);
}

?>