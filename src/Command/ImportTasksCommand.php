<?php

namespace App\Command;

use App\Entity\Task;
use App\Service\TaskApiClient1;
use App\Service\TaskApiClient2;
use Psr\Container\ContainerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ImportTasksCommand extends Command
{
    protected static $defaultName = 'task:import';
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this->setDescription('Import tasks from API into DB');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entityManager = $this->container->get('doctrine')->getManager();

        $apiTaskUrl1 = 'http://www.mocky.io/v2/5d47f24c330000623fa3ebfa';
        $apiTaskUrl2 = 'http://www.mocky.io/v2/5d47f235330000623fa3ebf7';

        $tasks = [];

        try {
            $apiClient1 = new TaskApiClient1($apiTaskUrl1);
            $apiClient1->fetchTasks();
            $tasks = array_merge($tasks, $apiClient1->getTasks());

            $apiClient2 = new TaskApiClient2($apiTaskUrl2);
            $apiClient2->fetchTasks();
            $tasks = array_merge($tasks, $apiClient2->getTasks());

            foreach ($tasks as $task) {
                $entityManager->persist($task);
            }

            $entityManager->flush();
        } catch (\Exception $exception) {
            $io->error($exception->getMessage());
            return 1;
        }

        $io->success('Tasks have been imported successfully!');
        return 0;
    }
}
