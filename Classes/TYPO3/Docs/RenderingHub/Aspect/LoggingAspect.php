<?php

namespace TYPO3\Docs\RenderingHub\Aspect;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * TODO this should be replaced by configuring the LogginTransport for Development context
 *
 * @Flow\Aspect
 */
class LoggingAspect {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Core\Bootstrap
	 */
	protected $bootstrap;

	/**
	 * Send email in production context only avoiding spam
	 *
	 * @param \TYPO3\Flow\Aop\JoinPointInterface $joinPoint
	 * @Flow\Around("method(TYPO3\Docs\RenderingHub\Log\.*Logger->email())")
	 * @return boolean
	 */
	public function sendEmail(\TYPO3\Flow\Aop\JoinPointInterface $joinPoint) {

		$result = FALSE;
		if ($this->bootstrap->getContext() == 'Production') {
			$result = TRUE;
		} else {
			$message = sprintf('Log: an email would have been sent in "Production" context but skipping email in "%s"', $this->bootstrap->getContext());
			\TYPO3\Docs\RenderingHub\Utility\Console::output($message);
		}
		return $result;
	}
}

?>
