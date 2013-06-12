<?php
namespace TYPO3\Docs\Finder;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving Uri
 *
 * @Flow\Scope("singleton")
 */
class Repository implements \TYPO3\Docs\Finder\Repository\FinderInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Finder\Repository\GitPackage
	 */
	protected $gitPackageFinder;

	/**
	 * Returns an URL of a repository given a package type
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUrl(\TYPO3\Docs\Domain\Model\Package $package) {
		return $this->getFinder($package)->getUrl($package);
	}

	/**
	 * Returns the proper finder for a package
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return \TYPO3\Docs\Finder\Repository\FinderInterface
	 */
	protected function getFinder(\TYPO3\Docs\Domain\Model\Package $package) {
		$repositoryType = $package->getRepositoryType();
		$finderName = $repositoryType . 'PackageFinder';
		return $this->$finderName;
	}
}

?>