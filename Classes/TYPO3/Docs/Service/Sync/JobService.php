<?php
namespace TYPO3\Docs\Service\Sync;

/*                                                                        *
 * This script belongs to the FLOW3 package "TYPO3.Docs".                 *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\FLOW3\Annotations as FLOW3;

/**
 * Class providing service related to a Job
 *
 */
class JobService {

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Queue\Job\JobManager
	 */
	protected $jobManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\FLOW3\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @FLOW3\Inject
	 * @var \TYPO3\Docs\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * Create a job for a synchronizing documentation files.
	 *
	 * @param string $packageKey of the document
	 * @return \TYPO3\Docs\Job\Sync\DocumentJob
	 */
	public function create($packageKey) {
		$job = new \TYPO3\Docs\Job\Sync\DocumentJob($packageKey);
		$job->setDryRun($this->runTimeSettings->getDryRun());
		return $job;
	}

	/**
	 * Put the job into the queue
	 *
	 * @param \TYPO3\Queue\Job\JobInterface $job
	 * @return void
	 */
	public function queue(\TYPO3\Queue\Job\JobInterface $job) {
		// Every object must be persisted prior to be put into the queue.
		$this->persistenceManager->persistAll();
		$this->jobManager->queue($job->getIdentifier(), $job);
	}

}
?>