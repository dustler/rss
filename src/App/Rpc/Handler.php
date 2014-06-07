<?php

namespace App\Rpc;

use App\Registry;
use App\Model;
use FastFeed\Factory;
use App\Model\Task;

class Handler
{
    public function create($task)
    {
        $taskId = $task['taskId'];
        $url = trim($task['config']['url']);

        return $this->addTask($taskId, $url);
    }

    protected function addTask($taskId, $url)
    {
        if (!$this->isValidFeed($url)) {
            echo "url: " . $url . " is not valid\n";
            return false;
        }

        $task = new Task();
        $task->setTaskId($taskId);
        $task->setUrl($url);
        $task->setCreatedAt(new \DateTime('NOW'));

        $this->addFeed($url);

        $entityManager = Registry::getEntityManager();

        $entityManager->persist($task);
        $entityManager->flush();

        return true;
    }

    protected function addFeed($url)
    {
//        if (!$this->isValidFeed($url)) {
//            echo "url: " . $url . " is not valid\n";
//            return false;
//        }

        $entityManager = Registry::getEntityManager();
        $resourceRep = $entityManager->getRepository("App\\Model\\Resource");
        $resource = $resourceRep->findOneBy(array(
            'link' => $url
        ));

        if ($resource !== null) {
            return false;
        }

        $newResource = new Model\Resource();
        $newResource->setLink($url);
        $entityManager->persist($newResource);
        $entityManager->flush();

        return true;
    }

    protected function isValidFeed($url)
    {
        try {
            $fastFeed = Factory::create();
            $fastFeed->addFeed('default', $url);
            $items = $fastFeed->fetch('default');
            var_dump($items);
            if (count($items)) {
                return true;
            }
        } catch (\Exception $e) {
            return false;
        }

        return false;
    }
}
