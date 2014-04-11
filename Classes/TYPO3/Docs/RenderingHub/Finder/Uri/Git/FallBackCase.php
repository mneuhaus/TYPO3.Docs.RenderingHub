<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri\Git;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class dealing with Uri coming from Git packages
 */
class FallBackCase extends \TYPO3\Docs\RenderingHub\Finder\Uri\AbstractCase {

	/**
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string
	 */
	public function handle(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package) {
		return sprintf('/%s/%s', $package->getUri(), $package->getVersion());
	}
}

?>