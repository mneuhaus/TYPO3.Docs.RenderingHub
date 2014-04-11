<?php
namespace TYPO3\Docs\RenderingHub\Hooks;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * Hook handler interface
 */
interface HookHandlerInterface {

	/**
	 * Returns the repository type of packages handled by this handler
	 *
	 * @return string
	 */
	public function getRepositoryType();

	/**
	 * Fetch packages using the available information
	 *
	 * @param \TYPO3\Flow\Mvc\ActionRequest $request
	 * @return \TYPO3\Docs\RenderingHub\Domain\Model\Package[]
	 */
	public function getPackages(\TYPO3\Flow\Mvc\ActionRequest $request);

}

?>