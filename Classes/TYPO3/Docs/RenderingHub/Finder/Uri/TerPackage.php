<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving Uri of a Ter package
 *
 * @Flow\Scope("singleton")
 */
class TerPackage implements FinderInterface {

	/**
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		return sprintf('/typo3cms/extensions/%s/%s', $package->getPackageKey(), $package->getVersion());
	}
}
?>