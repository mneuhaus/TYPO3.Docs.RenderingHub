<?php
namespace TYPO3\Docs\RenderingHub\Utility;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Utility class dealing with files
 *
 * @Flow\Scope("singleton")
 */
class Queue  {

	/**
	 * Get the log file given a repository type which can be git, ger
	 *
	 * @param string $repositoryType
	 * @return string
	 */
	public function getLogFile($repositoryType) {
		return sprintf('Data/Logs/Queue.%s.log', $repositoryType);
	}

	/**
	 * Get the Start command given a repository type and a log file
	 *
	 * @param string $repositoryType
	 * @return string
	 */
	public function getStartCommand($repositoryType) {
		$logFile = $this->getLogFile($repositoryType);
		return sprintf('nohup ./flow job:work %s >> %s 2>&1 &', $repositoryType, $logFile);
	}

	/**
	 * Send a signal in the log file
	 *
	 * @param string $signal
	 * @param string $repositoryType
	 * @return void
	 */
	public function writeToLogFile($signal, $repositoryType) {
		$logFile = $this->getLogFile($repositoryType);
		$message = "-------------------------------\n";
		$message .= sprintf("%sing queue handler at %s\n",
			ucfirst($signal),
			date('d-m-Y H:i'));

		file_put_contents($logFile, $message, FILE_APPEND);
	}

	/**
	 * Return an array of the process ids related flow command related to "job"
	 *
	 * @param array $repositoryTypes the repository types: git, ter, ...
	 * @return array
	 */
	public function getRunningProcesses($repositoryTypes) {
		$command = "ps -C php -o pid=,args= | grep job:work";
		exec($command, $processIds);
		$result = array();
		foreach ($processIds as $value) {

			preg_match('/([0-9]+) (.+)/is', $value, $matches);
			$pid = $matches[1];
			$command = $matches[2];

			// check if process is running, if not launch it!
			foreach ($repositoryTypes as $repositoryType) {
				if (strpos($command, $repositoryType)) {
					$result[$pid] = $repositoryType;
				}
			}
		}
		return $result;
	}

	/**
	 * Return whether the beanstalkd process is up or not.
	 *
	 * @return boolean
	 */
	public function isBeanstalkdProcess() {
		$command = "ps -C beanstalkd -o pid=,args=";
		exec($command, $processIds);
		return ! empty($processIds);
	}

	/**
	 * Return whether the beanstalkd process is up or not.
	 *
	 * @return boolean
	 */
	public function isOpenOfficeProcess() {
		$command = "ps -C soffice -o pid=,args=";
		exec($command, $processIds);
		return ! empty($processIds);
	}

}

?>