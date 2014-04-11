<?php
namespace TYPO3\Docs\RenderingHub\Finder\Uri;

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
	 * Returns an URI according to some rules
	 *
	 * @param \TYPO3\Docs\RenderingHub\Domain\Model\Package $package
	 * @return string the URI
	 */
	public function getUri(\TYPO3\Docs\RenderingHub\Domain\Model\Package $package);
}

?>