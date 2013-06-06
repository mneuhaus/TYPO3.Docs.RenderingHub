<?php
namespace TYPO3\Docs\Service\Build;

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
	 * Create a job for a rendering the documentation.
	 * Create the right type of job depending on whether the document is a Git or Ter package.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return \TYPO3\Queue\Job\JobInterface
	 */
	public function create(\TYPO3\Docs\Domain\Model\Document $document) {

		// Computes formats
		$className = sprintf('\TYPO3\Docs\Job\Build\%sDocumentJob', ucfirst($document->getRepositoryType()));
		$job = new $className($document);
		$job->setFormats($this->runTimeSettings->getFormats());
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