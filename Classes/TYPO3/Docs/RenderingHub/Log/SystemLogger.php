<?php

namespace TYPO3\Docs\RenderingHub\Log;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        */

use TYPO3\Flow\Annotations as Flow;

/**
 * A Logger Class
 *
 * @Flow\Scope("singleton")
 */
class SystemLogger {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\SwiftMailer\Message
	 */
	protected $message;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * Settings injection
	 *
	 * @param array $settings
	 * @return void
	 */
	public function injectSettings(array $settings) {
		$this->settings = $settings;
	}

	/**
	 * Writes the given message along with the additional information into the log.
	 *
	 * @param string $message The message to log
	 * @param integer $severity An integer value, one of the LOG_* constants
	 * @param mixed $additionalData A variable containing more information about the event to be logged
	 * @return void
	 * @api
	 */
	public function log($message, $severity = LOG_INFO, $additionalData = NULL) {

		switch ($severity) {
			case LOG_INFO:
				\TYPO3\Docs\RenderingHub\Utility\Console::output($message);
				break;
			case LOG_WARNING:
				\TYPO3\Docs\RenderingHub\Utility\Console::output('WARNING:' . $message);
				if ($this->settings['sendEmailForWarningMessage']) {
					$this->email($message, 'Warning');
				}
				break;
			case LOG_ALERT:
				\TYPO3\Docs\RenderingHub\Utility\Console::output('ALERT:' . $message);
				if ($this->settings['sendEmailForAlertMessage']) {
					$this->email($message, 'Alert');
				}
				break;
		}
	}

	/**
	 * Send an email
	 *
	 * @throws \TYPO3\Docs\RenderingHub\Exception\InvalidConfigurationException
	 * @param string $message The message to log
	 * @param string $severity
	 * @return void
	 * @api
	 */
	protected function email($message, $severity) {

		$subject = sprintf('[%s] %s triggered when rendering TYPO3 documentation', gethostname(), $severity);

		$this->message->setTo($this->settings['maintainers'])
			->setFrom($this->settings['sender'])
			->setSubject($subject)
			->setBody($message, 'text/plain');

		$this->message->send();

		if (!$this->message->isSent()) {
			throw new \TYPO3\Docs\RenderingHub\Exception\InvalidConfigurationException('No email has been sent. Check Swift Mailer configuration', 1345829105);
		}

	}
}

?>
