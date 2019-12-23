<?php

namespace App\Service;

class TaskApiClient1 extends AbstractTaskApiClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    public function __construct($taskURL)
    {
        parent::__construct($taskURL);
        $this->client = new \GuzzleHttp\Client();
    }

    public function fetchTasks()
    {
        $response = $this->client->request('GET', $this->taskURL);

        if ($response->getStatusCode() == 200) {
            $body = $response->getBody();
            $tasks = json_decode($body, true);

            foreach ($tasks as $taskItem) {
                $this->addTask($taskItem['id'], $taskItem['sure'], $taskItem['zorluk']);
            }
        }
    }
}