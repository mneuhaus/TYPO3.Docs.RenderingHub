<?php
namespace TYPO3\Docs\RenderingHub\Domain\Model;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Docs\RenderingHub\Domain\Model\AbstractTask;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;

/**
 * @Flow\Entity
 */
class Combo {
    const WAITING = 'waiting';
    const WORKING = 'working';
    const DONE = 'done';
    const FAILED = 'failed';

    /**
     * @var AbstractTask
     * @ORM\OneToOne
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    protected $rootTask;

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var string
     */
    protected $status;

    /**
     * @var array
     * @ORM\column(type="array")
     */
    protected $data;

    /**
     * @var \Doctrine\Common\Collections\Collection<AbstractTask>
     * @ORM\OneToMany(mappedBy="combo")
     */
    protected $tasks;

    /**
     * @Flow\Inject
     * @Flow\Transient
     * @var \TYPO3\Jobqueue\Common\Job\JobManager
     */
    protected $jobManager;

    /**
     * @Flow\Transient
     * @var AbstractTask
     */
    protected $lastCreatedTask;

    /**
     * @Flow\Transient
     * @Flow\Inject
     * @var PersistenceManagerInterface
     */
    protected $persistenceManager;

    /**
     */
    public function __construct() {
        $this->tasks = new ArrayCollection();
        $this->status = self::WAITING;
    }

    public function __toString() {
        return $this->name;
    }

    public function queue($task = NULL) {
        if ($this->persistenceManager->isNewObject($this)) {
            $this->persistenceManager->add($this);
        } else {
            $this->persistenceManager->update($this);
        }
        $this->persistenceManager->persistAll();

        if (!$task instanceof AbstractTask) {
            $task = $this->rootTask;
        }

        $this->jobManager->queue('org.typo3.docs.combo', $task);
    }

    public function execute() {
        $task = $this->rootTask;
        while ($task instanceof AbstractTask) {
            $status = $task->executeTask();
            if ($status == AbstractTask::FAILED) {
                break;
            }
            $task = $task->getNextTask();
        }
    }

    public function createTask($className) {
        $task = new $className();
        $task->setStatus('waiting');
        $this->addTask($task);

        if ($this->rootTask === NULL) {
            $this->rootTask = $task;
        } else {
            $this->lastCreatedTask->setNextTask($task);
        }

        $this->lastCreatedTask = $task;

        return $task;
    }

    /**
     * Gets name.
     *
     * @return string $name
     */
    public function getName() {
        return $this->name;
    }

    /**
     * Sets the name.
     *
     * @param string $name
     */
    public function setName($name) {
        $this->name = $name;
    }

    /**
     * Gets rootTask.
     *
     * @return AbstractTask $rootTask
     */
    public function getRootTask() {
        return $this->rootTask;
    }

    /**
     * Sets the rootTask.
     *
     * @param AbstractTask $rootTask
     */
    public function setRootTask($rootTask) {
        $this->rootTask = $rootTask;
    }

    /**
     * Add to the tasks.
     *
     * @param AbstractTask $task
     */
    public function addTask($task) {
        $task->setCombo($this);
        $this->tasks->add($task);
    }

    /**
     * Remove from tasks.
     *
     * @param AbstractTask $task
     */
    public function removeTask($task) {
        $this->tasks->remove($task);
    }

    /**
     * Gets tasks.
     *
     * @return \Doctrine\Common\Collections\Collection<AbstractTask> $tasks
     */
    public function getTasks() {
        return $this->tasks;
    }

    /**
     * Sets the tasks.
     *
     * @param \Doctrine\Common\Collections\Collection<AbstractTask> $tasks
     */
    public function setTasks($tasks) {
        $this->tasks = $tasks;
    }

    /**
     * @param array $data
     */
    public function setData($data) {
        $this->data = $data;
    }

    /**
     * @return array
     */
    public function getData() {
        return $this->data;
    }

    /**
     * @param string $status
     */
    public function setStatus($status) {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getStatus() {
        return $this->status;
    }

}