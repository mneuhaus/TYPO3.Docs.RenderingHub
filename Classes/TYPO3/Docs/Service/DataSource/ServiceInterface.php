<?php
namespace TYPO3\Docs\Service\DataSource;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Interface for Data Source Service
 */
interface ServiceInterface {

	/**
	 * Returns a bunch of data coming from the data-source.
	 *
	 * @return mixed
	 */
	public function get();

	/**
	 * Update the local data-source.
	 * Before updating from remote host check whether the cached file is obsolete.
	 * Returns TRUE if we write a new data-source file.
	 *
	 * @return boolean
	 */
	public function update();
}

?>