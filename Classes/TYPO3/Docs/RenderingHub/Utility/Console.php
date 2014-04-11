<?php
namespace TYPO3\Docs\RenderingHub\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with the command line
 *
 * @Flow\Scope("singleton")
 */
class Console {

	/**
	 * Whether the commands should be run or "only" displayed in the terminal.
	 *
	 * @var bool
	 */
	public static $dryRun = FALSE;

	/**
	 * Ask if the User agrees with the message. If the force option is set to TRUE override the message
	 *
	 * @param string $message
	 * @param boolean $force
	 * @return bool
	 */
	public static function askUserValidation($message, $force = FALSE) {

		if ($force) {
			return TRUE;
		}

		print($message);

		// Read the reply of the User from the console
		$reply = strtolower(trim(fgets(STDIN)));
		if ($reply === 'y' || $reply === 'yes') {
			return TRUE;
		}

		return FALSE;
	}

	/**
	 * Output message on the console.
	 *
	 * @param string $message
	 * @return void
	 */
	public static function output($message = '') {
		if (is_array($message) || is_object($message)) {
			print_r($message);
		} elseif (is_bool($message)) {
			var_dump($message);
		} else {
			print $message . PHP_EOL;
		}
	}

	/**
	 * Run a command
	 *
	 * @param string $command the command to be executed
	 * @return array
	 */
	public static function run($command) {
		$output = array();
		if (!self::$dryRun) {
			exec($command, $output, $return);
			if (!empty($output)) {
				self::output(implode("\n", $output));
			}
		} else {
			self::output($command);
		}
		return $output;
	}
}

?>