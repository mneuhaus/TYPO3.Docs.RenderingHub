<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Utility class dealing with status message
 *
 * @FLOW3\Scope("singleton")
 */
class StatusMessage  {

	const RENDER = 'waiting-rendering';
	const OK = 'OK';
	const SYNC = 'waiting-sync';
	const NOT_FOUND = 'documentation-not-found';

}

?>