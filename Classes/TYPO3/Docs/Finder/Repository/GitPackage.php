<?php
namespace TYPO3\Docs\Finder\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving Uri of a Git package
 *
 * @Flow\Scope("singleton")
 */
class GitPackage implements \TYPO3\Docs\Finder\Repository\FinderInterface {

	/**
	 * Returns an URL of a repository given a package type
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUrl(\TYPO3\Docs\Domain\Model\Package $package) {
		// TODO: Implement getUrl() method.
	}}
?>