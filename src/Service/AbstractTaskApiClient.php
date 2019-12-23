<?php


namespace App\Service;


use App\Entity\Task;

abstract class AbstractTaskApiClient
{
    protected $taskURL;
    protected $tasks = [];

    public function __construct($taskURL)
    {
        $this->taskURL = $taskURL;
    }

    public function getTasks()
    {
        return $this->tasks;
    }

    abstract public function fetchTasks();

    /**
     * @param $taskId
     * @param $taskTime
     * @param $taskDifficulty
     */
    public function addTask($taskId, $taskTime, $taskDifficulty)
    {
        $task = new Task();
        $task->setTaskName($taskId)
            ->setTaskTime($taskTime)
            ->setTaskDifficulty($taskDifficulty);

        array_push($this->tasks, $task);
    }
}