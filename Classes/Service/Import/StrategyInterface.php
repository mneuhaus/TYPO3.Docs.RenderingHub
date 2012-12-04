<?php
namespace TYPO3\Docs\Service\Import;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

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