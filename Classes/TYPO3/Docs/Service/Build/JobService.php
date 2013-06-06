<?php
namespace TYPO3\Docs\Service\Build;

/*                                                                        *
 * This script belongs to the TYPO3 Flow package "TYPO3.Docs".            *
 *                                                                        *
 *                                                                        *
 */

use TYPO3\Flow\Annotations as Flow;

/**
 * Class providing service related to a Job
 *
 */
class JobService {

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Jobqueue\Common\Job\JobManager
	 */
	protected $jobManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Flow\Persistence\PersistenceManagerInterface
	 */
	protected $persistenceManager;

	/**
	 * @Flow\Inject
	 * @var \TYPO3\Docs\Utility\RunTimeSettings
	 */
	protected $runTimeSettings;

	/**
	 * Create a job for a rendering the documentation.
	 * Create the right type of job depending on whether the document is a Git or Ter package.
	 *
	 * @param \TYPO3\Docs\Domain\Model\Document $document
	 * @return \TYPO3\Jobqueue\Common\Job\JobInterface
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
	 * @param \TYPO3\Jobqueue\Common\Job\JobInterface $job
	 * @return void
	 */
	public function queue(\TYPO3\Jobqueue\Common\Job\JobInterface $job) {
		// Every object must be persisted prior to be put into the queue.
		$this->persistenceManager->persistAll();
		$this->jobManager->queue($job->getIdentifier(), $job);
	}

}
?>