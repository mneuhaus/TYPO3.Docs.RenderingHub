<?php
namespace TYPO3\Docs\RenderingHub\Finder;

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
class Uri implements \TYPO3\Docs\RenderingHub\Finder\Uri\FinderInterface {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Uri\GitPackage
	 */
	protected $gitPackageFinder;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Finder\Uri\TerPackage
	 */
	protected $terPackageFinder;

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		return $this->getFinder($package)->getUri($package);
	}

	/**
	 * Returns the proper finder for a package
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return \TYPO3\Docs\RenderingHub\Finder\Uri\FinderInterface
	 */
	protected function getFinder(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		$repositoryType = $package->getRepositoryType();
		$finderName = $repositoryType . 'PackageFinder';
		return $this->$finderName;
	}
}

?>