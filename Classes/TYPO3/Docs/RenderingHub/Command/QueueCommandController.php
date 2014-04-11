<?php
namespace TYPO3\Docs\RenderingHub\Command;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Docs\RenderingHub\Utility\ColorCli;
use TYPO3\Docs\RenderingHub\Utility\Console;
use TYPO3\Flow\Annotations as Flow;

/**
 * Queue management command controller.
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
	 * @var \TYPO3\Docs\RenderingHub\Log\UserLogger
	 */
	protected $logger;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Jobqueue\Common\Queue\QueueManager
	 */
	protected $queueManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\RenderingHub\Utility\Queue
	 */
	protected $queueUtility;

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
	 * Make sure all queues are up.
	 *
	 * If a queue is down, re-launch the process and send a notice to the admin for notification.
	 * A logging file will be created upon starting in Data/Logs/Queue.*.log.
	 *
	 * This command should be called in a Cron job with a frequency of 5 minutes (for example).
	 *
	 * More help can be found on the Wiki http://forge.typo3.org/projects/team-doc-rendering/wiki/Management
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
					Console::run($command);

						// send email to Maintainers that a process was started
					$subject = sprintf('TYPO3 documentation queue "%s" has been re-started on server "%s"', $queue, gethostname());
					$body = "Hello,\nThis message could happen because the server was restarted or because the job made the queue existing. Checkout if there are further notices";

					$this->logger->log($subject, $body, $this->settings['maintainers']);
				}
			}

			$this->statusCommand();
		} else {
			if (! $this->queueUtility->isBeanstalkdProcess()) {
				$message = sprintf('%s Beanstalkd daemon is not running. Can you start it?', ColorCli::getColoredString('[FAILED]', 'red'));
				Console::output($message);
			} elseif (! $this->queueUtility->isOpenOfficeProcess()){
				$message = sprintf('%s OpenOffice daemon is not running. Can you start it?', ColorCli::getColoredString('[FAILED]', 'red'));
				Console::output($message);
			}
		}
	}

	/**
	 * Restart all queues.
	 *
	 * This is just a shortcut for stop followed by start.
	 *
	 * @return void
	 */
	public function restartCommand() {
		$this->stopCommand();
		$this->startCommand();
	}

	/**
	 * Check the status of all queues.
	 *
	 * @return void
	 */
	public function statusCommand() {

		$processes = $this->queueUtility->getRunningProcesses($this->queues);
		if (count($processes) === count($this->queues)) {

			$message = sprintf('%s "%s" queues are up on pid "%s".',
				ColorCli::getColoredString('[OK]', 'green'),
				implode('", "', $this->queues),
				implode('", "', array_keys($processes))
			);
		} elseif (count($processes) > count($this->queues)) {
			$message = sprintf('%s More queue handlers are running than they should. Try stopping and restarting the queues',
				ColorCli::getColoredString('[PARTIAL]', 'yellow'));
		} elseif (count($processes) === 0) {
			$message = sprintf('%s no queue handler is running.', ColorCli::getColoredString('[KO]', 'red'));
		} else {
			$message = sprintf('%s It should be more queue handlers running, check out system processes for further investigation.',
				ColorCli::getColoredString('[PARTIAL]', 'yellow'));
		}
		Console::output($message);
	}

	/**
	 * Stop all queues.
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
					ColorCli::getColoredString('[FAILED]', 'red'),
					$processId);
			} else {
				$message = sprintf('%s Queue "%s" has been stopped',
					ColorCli::getColoredString('[OK]', 'green'),
					$queue
				);
			}
			Console::output($message);
		}
	}

	/**
	 * Displays statistics of all queues.
	 *
	 * @return void
	 */
	public function statsCommand() {

		foreach ($this->queues as $queue) {
			$statistics = $this->queueManager->getQueue($queue)->getStatistics();
			$upTimeInDays = round(($statistics['uptime'] / 86400), 2);
			$totalJobs = $statistics['total-jobs'] - $statistics['current-jobs-ready'];

			$message = <<<EOF

Queue "{$queue}"
------------------------------------------
Jobs ready:    {$statistics['current-jobs-ready']}
Jobs delayed:  {$statistics['current-jobs-delayed']}
Jobs achieved: {$totalJobs}
Uptime:        {$statistics['uptime']} sec ({$upTimeInDays} days)
Tubes:         {$statistics['current-tubes']}
Queue PID:     {$statistics['pid']}

EOF;
			Console::output($message);
		}
	}
}

?>