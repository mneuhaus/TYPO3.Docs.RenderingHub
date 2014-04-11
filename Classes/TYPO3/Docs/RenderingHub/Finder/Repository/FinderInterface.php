<?php
namespace TYPO3\Docs\RenderingHub\Finder\Repository;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class for resolving uri
 */
interface FinderInterface {

	/**
	 * Returns an URL of a repository given a package type
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUrl(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package);
}

?>