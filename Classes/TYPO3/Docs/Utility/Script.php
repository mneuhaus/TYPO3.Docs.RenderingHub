<?php
namespace TYPO3\Docs\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with Script. This class was used at some point but not anymore currently. It can be probably removed.
 * However, method "executeCommand" was taken from Flow and slightly changed enabling to
 * pass additional settings. @todo report this modification before removing the class
 *
 * @Flow\Scope("singleton")
 */
class Script {

	/**
	 * Executes the given command as a sub-request to the FLOW3 CLI system.
	 * Note: adding handling of arguments key in $settings for passing parameter
	 *
	 * @param string $commandIdentifier E.g. typo3.flow3:cache:flush
	 * @param array $settings The FLOW3 settings
	 * @param boolean $outputResults if FALSE the output of this command is only echoed if the execution was not successful
	 * @return boolean TRUE if the command execution was successful (exit code = 0)
	 * @see \TYPO3\Flow\Core\Booting\Scripts::executeCommand
	 * @api
	 */
	static public function executeCommand($commandIdentifier, array $settings, $outputResults = TRUE) {
		$subRequestEnvironmentVariables = array(
			'FLOW_ROOTPATH' => FLOW_PATH_ROOT,
			'FLOW_CONTEXT' => $settings['core']['context']
		);
		if (isset($settings['core']['subRequestEnvironmentVariables'])) {
			$subRequestEnvironmentVariables = array_merge($subRequestEnvironmentVariables, $settings['core']['subRequestEnvironmentVariables']);
		}

		$command = '';
		foreach ($subRequestEnvironmentVariables as $argumentKey => $argumentValue) {
			if (DIRECTORY_SEPARATOR === '/') {
				$command .= sprintf('%s=%s ', $argumentKey, escapeshellarg($argumentValue));
			} else {
				$command .= sprintf('SET %s=%s&', $argumentKey, escapeshellarg($argumentValue));
			}
		}

		$additionalArguments = '';
		if (! empty($settings['core']['arguments']) && is_array($settings['core']['arguments'])) {
			foreach ($settings['core']['arguments'] as $argumentKey => $argumentValue) {
				$additionalArguments .= sprintf('%s=%s ', $argumentKey, escapeshellarg($argumentValue));
			}
		}

		$phpBinaryPathAndFilename = escapeshellcmd(\TYPO3\Flow\Utility\Files::getUnixStylePath($settings['core']['phpBinaryPathAndFilename']));
		$command .= sprintf('"%s" -c %s %s %s %s', $phpBinaryPathAndFilename, escapeshellarg(php_ini_loaded_file()), escapeshellarg(FLOW_PATH_FLOW . 'Scripts/flow3.php'), escapeshellarg($commandIdentifier), $additionalArguments);
		$output = array();
		exec($command, $output, $result);
		if ($outputResults || $result !== 0) {
			echo implode(PHP_EOL, $output);
		}
		return $result === 0;
	}
}

?>