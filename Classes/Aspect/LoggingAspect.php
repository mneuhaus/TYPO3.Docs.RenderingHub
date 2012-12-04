<?php

namespace TYPO3\Docs\Aspect;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * @FLOW3\Aspect
 */
class LoggingAspect {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Send email in production context only avoiding spam
	 *
	 * @param \TYPO3\FLOW3\AOP\JoinPointInterface $joinPoint
	 * @FLOW3\Around("method(TYPO3\Docs\Log\.*Logger->email())")
	 * @return boolean
	 */
	public function sendEmail(\TYPO3\FLOW3\AOP\JoinPointInterface $joinPoint) {

		$result = FALSE;
		if ($this->bootstrap->getContext() == 'Production') {
			$result = TRUE;
		} else {
			$message = sprintf('Log: an email would have been sent in "Production" context but skipping email in "%s"', $this->bootstrap->getContext());
			\TYPO3\Docs\Utility\Console::output($message);
		}
		return $result;
	}
}

?>
