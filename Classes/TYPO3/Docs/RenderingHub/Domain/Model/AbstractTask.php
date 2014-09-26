<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Docs\RenderingHub\Domain\Model\Combo;
use TYPO3\Docs\RenderingHub\Log\SystemLogger;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Jobqueue\Common\Job\JobInterface;
use TYPO3\Jobqueue\Common\Queue\Message;
use TYPO3\Jobqueue\Common\Queue\QueueInterface;

/**
 * @Flow\Entity
 * @ORM\InheritanceType("JOINED")
 */
abstract class AbstractTask implements JobInterface {
    const WAITING = 'waiting';
    const WORKING = 'working';
    const DONE = 'done';
    const FAILED = 'failed';

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\Combo
     * @ORM\ManyToOne(inversedBy="tasks")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $combo;

    /**
     * @var string
     */
    protected $status;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask
     * @ORM\OneToOne(mappedBy="previousTask")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $nextTask;

    /**
     * @var \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask
     * @ORM\OneToOne(mappedBy="nextTask")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $previousTask;

    /**
     * @Flow\Inject
     * @Flow\Transient
     * @var SystemLogger
     */
    protected $systemLogger;

    /**
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;


    public function __construct() {
        $this->status = self::WAITING;
    }

    public function __toString() {
        $parts = explode('\\', get_class($this));
        $className = array_pop($parts);
        $className = str_replace('Task', '', $className);
        $className = preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $className);
        return $className;
    }

    /**
     * Execute the job
     *
     * A job should finish itself after successful execution using the queue methods.
     *
     * @param QueueInterface $queue
     * @param Message $message The original message
     * @return boolean TRUE if the job was executed successfully and the message should be finished
     */
    public function execute(QueueInterface $queue, Message $message) {
        $this->combo->setStatus(Combo::WORKING);

        $status = $this->executeTask();
        $this->persistenceManager->persistAll();

        if ($status == AbstractTask::FAILED) {
            $this->combo->setStatus(Combo::FAILED);
            $this->persistenceManager->update($this->combo);
            $this->persistenceManager->persistAll();
            return false;
        }

        if ($this->nextTask !== NULL) {
            $this->combo->queue($this->nextTask);
        } else {
            $this->combo->setStatus(Combo::DONE);
        }
        return true;
    }

    /**
     * Get an optional identifier for the job
     *
     * @return string A job identifier
     */
    public function getIdentifier() {
        return get_class($this);
    }

    /**
     * Get a readable label for the job
     *
     * @return string A label for the job
     */
    public function getLabel() {
        $parts = explode('\\', get_class($this));
        $className = array_pop($parts);
        $className = str_replace('Task', '', $className);
        $className = preg_replace('/([^A-Z])([A-Z])/', "$1 $2", $className);
        return $className;
    }

    /**
     * Gets combo.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\Combo $combo
     */
    public function getCombo() {
        return $this->combo;
    }

    /**
     * Sets the combo.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\Combo $combo
     */
    public function setCombo($combo) {
        $this->combo = $combo;
    }

    /**
     * Gets nextTask.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask $nextTask
     */
    public function getNextTask() {
        return $this->nextTask;
    }

    /**
     * Sets the nextTask.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask $nextTask
     */
    public function setNextTask($nextTask) {
        $nextTask->setPreviousTask($this);
        $this->nextTask = $nextTask;
    }

    /**
     * Gets previousTask.
     *
     * @return \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask $previousTask
     */
    public function getPreviousTask() {
        return $this->previousTask;
    }

    /**
     * Sets the previousTask.
     *
     * @param \TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask $previousTask
     */
    public function setPreviousTask($previousTask) {
        $this->previousTask = $previousTask;
    }

    /**
     * Gets status.
     *
     * @return string $status
     */
    public function getStatus() {
        return $this->status;
    }

    /**
     * Sets the status.
     *
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

}