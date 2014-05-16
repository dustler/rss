<?php

namespace App;

use DateTime;
use FastFeed\Factory;

class Reader
{
    /**
     * @return DateTime
     */
    public function getLastCheckTime()
    {
        $time = new DateTime();

        $fileName = APP_DIR . "/last_check_time";

        if (!file_exists($fileName)) {
            $time->setTimestamp(time() - (5 * 60));
        } else {
            $timestamp = file_get_contents($fileName);
            $time->setTimestamp($timestamp);
        }

        return $time;
    }

    public function setLastCheckTime(DateTime $time)
    {
        $fileName = APP_DIR . "/last_check_time";

        if (!file_exists($fileName)) {
            touch($fileName);
        }

        file_put_contents($fileName, $time->getTimestamp());
    }

    public function addFeed($url)
    {
        if (!$this->isValidFeed($url)) {
            echo "url: " . $url . " is not valid\n";
            return false;
        }


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