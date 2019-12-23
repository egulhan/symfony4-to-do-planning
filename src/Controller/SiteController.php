<?php

namespace App\Controller;

use App\Entity\Task;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SiteController extends AbstractController
{
    // TODO: better to store on DB (reversed)
    public static $developers = [
        ['name' => 'DEV5', 'time' => 1, 'difficulty' => 5],
        ['name' => 'DEV4', 'time' => 1, 'difficulty' => 4],
        ['name' => 'DEV3', 'time' => 1, 'difficulty' => 3],
        ['name' => 'DEV2', 'time' => 1, 'difficulty' => 2],
        ['name' => 'DEV1', 'time' => 1, 'difficulty' => 1],
    ];

    /**
     * @Route("/", name="site.index")
     */
    public function index()
    {
        // TODO: better to break business logic
        $dailyDevWorkTime = $_ENV['DAILY_DEV_WORK_TIME'];
        $em = $this->getDoctrine()->getManager();
        $tasks = $em->getRepository(Task::class)->getSorted();
        $taskCount = count($tasks);
        $allNotAssigned = true;

        $plan = ['weeks' => []];
        $developers = self::$developers;

        $week = $day = 0;
        $assignedTasks = [];
        while ($allNotAssigned) {
            if (!isset($plan['weeks'][$week])) {
                $plan['weeks'][$week] = [
                    'hasWeekPlanCompleted' => false,
                    'days' => [],
                ];
            }

            if (!isset($plan['weeks'][$week]['days'][$day])) {
                $plan['weeks'][$week]['days'][$day] = [
                    'hasDayPlanCompleted' => false,
                    'developers' => [],
                ];
            }

            $currentWeek = $plan['weeks'][$week];
            $currentDay = $currentWeek['days'][$day];
            for ($taskIndex = 0; $taskIndex < $taskCount; $taskIndex++) {
                $task = $tasks[$taskIndex];

                if (in_array($task->getTaskName(), $assignedTasks)) {
                    continue;
                }

                $isLastTask = $taskIndex == $taskCount - 1;
                $taskDifficulty = $task->getTaskDifficulty();

                for ($devIndex = 0; $devIndex < count($developers); $devIndex++) {
                    $dev = $developers[$devIndex];
                    $devName = $dev['name'];

                    if (!isset($currentDay['developers'][$devName])) {
                        $currentDay['developers'][$devName] = [
                            'devName' => $devName,
                            'isFull' => false,
                            'totalWorkTime' => 0,
                            'tasks' => [],
                        ];
                    }

                    $devTaskCompletedTime = $taskDifficulty / $dev['difficulty'];
                    $devTotalWorkTime = $currentDay['developers'][$devName]['totalWorkTime'];

                    if ($currentDay['developers'][$devName]['isFull']) {
                        // next dev.
                        continue;
                    }

                    if ($devTotalWorkTime + $devTaskCompletedTime > $dailyDevWorkTime) {
                        // next task
                        continue;
                    }

                    $currentDay['developers'][$devName]['totalWorkTime'] += $devTaskCompletedTime;
                    $currentDay['developers'][$devName]['tasks'][] = [
                        'name' => $task->getTaskName(),
                        'time' => $task->getTaskTime(),
                        'difficulty' => $taskDifficulty,
                        'taskCompletedTime' => $devTaskCompletedTime,
                    ];
                    $assignedTasks[] = $task->getTaskName();

                    if (bccomp($currentDay['developers'][$devName]['totalWorkTime'], floatval($dailyDevWorkTime), 2) == 0) {
                        $currentDay['developers'][$devName]['isFull'] = true;
                    }

                    break;
                }

                $currentWeek['days'][$day] = $currentDay;
            }

            $plan['weeks'][$week] = $currentWeek;

            if (count($assignedTasks) == $taskCount) {
                $allNotAssigned = false;
            } else {
                $this->increaseDayOrWeek($week, $day);
            }
        }

        return $this->render('site/index.html.twig', [
            'plan' => $plan,
        ]);
    }

    protected function increaseDayOrWeek(&$week, &$day)
    {
        if ($day < 4) {
            $day++;
        } else {
            $day = 0;
            $week++;
        }
    }
}
