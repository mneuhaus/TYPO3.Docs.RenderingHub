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
class UserLogger {

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
	 * Log message for a user. The common case is to send an email but only in production context.
	 *
	 * @param string $message The message to log
	 * @param string $additionalData A variable containing more information about the event to be logged
	 * @param array $recipients a list of email address to sent the log
	 * @return void
	 * @api
	 */
	public function log($message, $additionalData, $recipients) {
		$this->email($message, $additionalData, $recipients);
	}

	/**
	 * Send an email
	 *
	 * @throws \TYPO3\Docs\RenderingHub\Exception\InvalidConfigurationException
	 * @param string $subject of the message
	 * @param string $body of the message
	 * @param array $recipients
	 * @return void
	 * @api
	 */
	public function email($subject, $body, $recipients) {

		// Send email in production context only to avoid spamming
		$this->message->setTo($recipients)
			->setFrom($this->settings['sender'])
			->setSubject($subject)
			->setBody($body, 'text/plain');

		$this->message->send();

		if (!$this->message->isSent()) {
			throw new \TYPO3\Docs\RenderingHub\Exception\InvalidConfigurationException('No email has been sent. Check Swift Mailer configuration', 1349540098);
		}
	}
}

?>
