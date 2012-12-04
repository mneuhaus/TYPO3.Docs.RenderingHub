<?php
namespace TYPO3\Docs\Finder\Uri;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class for resolving Uri of a Git package
 *
 * @FLOW3\Scope("singleton")
 */
class GitPackage implements \TYPO3\Docs\Finder\Uri\FinderInterface {

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\Domain\Model\Package $package) {

		$typo3CmsExtensionsCase = new \TYPO3\Docs\Finder\Uri\Git\Typo3CmsExtensionCase();
		$typo3CmsDocumentationCase = new \TYPO3\Docs\Finder\Uri\Git\Typo3CmsDocumentationCase();
		$fallBackCase = new \TYPO3\Docs\Finder\Uri\Git\FallBackCase();

		$typo3CmsExtensionsCase->setSuccessor($typo3CmsDocumentationCase);
		$typo3CmsDocumentationCase->setSuccessor($fallBackCase);

		return $typo3CmsExtensionsCase->handle($package);
	}

	/**
	 * Returns the URL repository
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getRepositoryUrl(\TYPO3\Docs\Domain\Model\Package $package) {

	}
}
?>