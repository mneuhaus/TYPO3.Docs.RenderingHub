<?php
namespace TYPO3\Docs\Finder;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class for resolving Uri
 *
 * @FLOW3\Scope("singleton")
 */
class Uri implements \TYPO3\Docs\Finder\Uri\FinderInterface {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Uri\GitPackage
	 */
	protected $gitPackageFinder;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Finder\Uri\TerPackage
	 */
	protected $terPackageFinder;

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\Domain\Model\Package $package) {
		return $this->getFinder($package)->getUri($package);
	}

	/**
	 * Returns the proper finder for a package
	 *
	 * @param \TYPO3\Docs\Domain\Model\Package $package
	 * @return \TYPO3\Docs\Finder\Uri\FinderInterface
	 */
	protected function getFinder(\TYPO3\Docs\Domain\Model\Package $package) {
		$repositoryType = $package->getRepositoryType();
		$finderName = $repositoryType . 'PackageFinder';
		return $this->$finderName;
	}
}

?>