<?php
namespace TYPO3\Docs\RenderingHub\Service\Import;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Standard controller for the TYPO3.Docs package
 *
 */
interface StrategyInterface  {

	/**
	 * Retrieve a TYPO3 package given a package name and its possible versions and then render them.
	 *
	 * @param string $packageKey the package name
	 * @param string $version the package name
	 * @return void
	 */
	public function import($packageKey, $version = '');

	/**
	 * Retrieve all TYPO3 packages from a repository and render them.
	 *
	 * @return void
	 */
	public function importAll();
}

?>