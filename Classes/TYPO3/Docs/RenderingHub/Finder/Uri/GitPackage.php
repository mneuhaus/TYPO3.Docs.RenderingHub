<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri;

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
class GitPackage implements FinderInterface {

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {

		$typo3CmsExtensionsCase = new \TYPO3\Docs\RenderingHub\Finder\Uri\Git\Typo3CmsExtensionCase();
		$typo3CmsDocumentationCase = new \TYPO3\Docs\RenderingHub\Finder\Uri\Git\Typo3CmsDocumentationCase();
		$fallBackCase = new \TYPO3\Docs\RenderingHub\Finder\Uri\Git\FallBackCase();

		$typo3CmsExtensionsCase->setSuccessor($typo3CmsDocumentationCase);
		$typo3CmsDocumentationCase->setSuccessor($fallBackCase);

		return $typo3CmsExtensionsCase->handle($package);
	}

	/**
	 * Returns the URL repository
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getRepositoryUrl(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {

	}
}
?>