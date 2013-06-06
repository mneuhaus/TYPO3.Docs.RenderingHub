<?php
namespace TYPO3\Docs\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Document rendering command controller
 * to be used as a basis for the documentation rendering by the doc team
 *
 * @Flow\Scope("singleton")
 */
class QueueCommandController extends \TYPO3\Flow\Cli\CommandController {

	/**
	 * @var array
	 */
	protected $queues = array(
		'git',
//		'sync',
	);

	/**
	 * A reference to an User Logger
	 *
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Log\UserLogger
	 */
	protected $logger;

	/**
	 * @var array
	 */
	protected $settings;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Queue\QueueManager
	 */
	protected $queueManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Utility\Queue
	 */
	protected $queueUtility;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Configuration\ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * Further object initialization
	 */
	public function initializeObject() {
		$this->settings = $this->configurationManager->getConfiguration();
	}

	/**
	 * Display a help message
	 *
	 * @return void
	 */
	public function helpCommand() {

		$message = <<<EOF

Useful commands for handling queue.

Usage:
-------

./flow3 queue:{start | stop | status| restart}

"status" and "stop" commands are self-explanatory. "start" command acts also
as "keep-a-live" making sure the queue is up. If the queue is down, the command
will try to re-initialize a process. An email will also be sent to
maintainers (cf. configuration). A logging file is created at Data/Logs/Queue.*

More help can be found on the Wiki
http://forge.typo3.org/projects/team-doc-rendering/wiki/Management

EOF;
		$this->outputLine($message);
	}

	/**
	 * Make sure the queue is up. If not, re-launch the process and send a notice to the admin for notification.
	 * This command should be called in a Cron job with a frequency of 5 minutes (for example).
	 * A logging file will be created upon starting in Data/Logs/Aueue.*.log
	 *
	 * @return void
	 */
	public function startCommand() {

		if ($this->queueUtility->isBeanstalkdProcess() && $this->queueUtility->isOpenOfficeProcess()) {

			// Get possible running process
			$processes = $this->queueUtility->getRunningProcesses($this->queues);

			foreach ($this->queues as $queue) {

				// check if process is running, if not launch it!
				if (! in_array($queue, $processes)) {
					$this->queueUtility->writeToLogFile('start', $queue);

					$command = $this->queueUtility->getStartCommand($queue);
					\TYPO3\Docs\Utility\Console::run($command);

					// send email to Maintainers that a process was started
					$subject = sprintf('TYPO3 documentation queue "%s" has been re-started on server "%s"', $queue, gethostname());
					$body = "Hello,\nThis message could happen because the server was restarted or because the job made the queue existing. Checkout if there are further notices";

					$this->logger->log($subject, $body, $this->settings['maintainers']);
				}
			}

			$this->statusCommand();
		} else {
			if (! $this->queueUtility->isBeanstalkdProcess()) {
				$message = sprintf('%s Beanstalkd daemon is not running. Can you start it?',
					\TYPO3\Docs\Utility\ColorCli::getColoredString('[FAILED]', 'red'));
				\TYPO3\Docs\Utility\Console::output($message);
			} elseif (! $this->queueUtility->isOpenOfficeProcess()){
				$message = sprintf('%s OpenOffice daemon is not running. Can you start it?',
					\TYPO3\Docs\Utility\ColorCli::getColoredString('[FAILED]', 'red'));
				\TYPO3\Docs\Utility\Console::output($message);
			}
		}
	}

	/**
	 * Restart the queue handler services
	 *
	 * @return void
	 */
	public function restartCommand() {
		$this->stopCommand();
		$this->startCommand();
	}

	/**
	 * Check the status of the queue
	 *
	 * @return void
	 */
	public function statusCommand() {

		$processes = $this->queueUtility->getRunningProcesses($this->queues);
		if (count($processes) == count($this->queues)) {

			$message = sprintf('%s "%s" queues are up on pid "%s".',
				\TYPO3\Docs\Utility\ColorCli::getColoredString('[OK]', 'green'),
				implode('", "', $this->queues),
				implode('", "', array_keys($processes))
			);
		} elseif (count($processes) > count($this->queues)) {
			$message = sprintf('%s More queue handlers are running than they should. Try stopping and restarting the queues',
				\TYPO3\Docs\Utility\ColorCli::getColoredString('[PARTIAL]', 'yellow'));
		} elseif (count($processes) == 0) {
			$message = sprintf('%s no queue handler is running.', \TYPO3\Docs\Utility\ColorCli::getColoredString('[KO]', 'red'));
		} else {
			$message = sprintf('%s It should be more queue handlers running, check out system processes for further investigation.',
				\TYPO3\Docs\Utility\ColorCli::getColoredString('[PARTIAL]', 'yellow'));
		}
		\TYPO3\Docs\Utility\Console::output($message);
	}

	/**
	 * Stop all queues
	 *
	 * @return void
	 */
	public function stopCommand() {
		$processes = $this->queueUtility->getRunningProcesses($this->queues);

		foreach ($processes as $processId => $queue) {

			$this->queueUtility->writeToLogFile('stop', $queue);
			$result = posix_kill($processId, 9);

			if ($result === FALSE) {
				$message = sprintf('%s Killing process id "%s" returned FALSE. Weird!',
					\TYPO3\Docs\Utility\ColorCli::getColoredString('[FAILED]', 'red'),
					$processId);
			} else {
				$message = sprintf('%s Queue "%s" has been stopped',
					\TYPO3\Docs\Utility\ColorCli::getColoredString('[OK]', 'green'),
					$queue
				);
			}
			\TYPO3\Docs\Utility\Console::output($message);
		}
	}

	/**
	 * Displays statistics of the queue
	 *
	 * @return void
	 */
	public function statsCommand() {

		foreach ($this->queues as $queue) {
			$stats = $this->queueManager->stats($queue);
			$upTimeInDay = round(($stats['uptime'] / 86400), 2);
			$totalJobs = $stats['total-jobs'] - $stats['current-jobs-ready'];

			$message = <<<EOF

Queue "{$queue}"
---------------------
Jobs ready: {$stats['current-jobs-ready']}
Jobs delayed: {$stats['current-jobs-delayed']}
Jobs achieved: {$totalJobs}
Up-time: {$stats['uptime']} sec ({$upTimeInDay} days)
Number of tube: {$stats['current-tubes']}
Pid of the queue: {$stats['pid']}

EOF;
			\TYPO3\Docs\Utility\Console::output($message);
		}
	}
}

?>