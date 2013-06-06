<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with status message
 *
 * @Flow\Scope("singleton")
 */
class StatusMessage  {

	const RENDER = 'waiting-rendering';
	const OK = 'OK';
	const SYNC = 'waiting-sync';
	const NOT_FOUND = 'documentation-not-found';

}

?>