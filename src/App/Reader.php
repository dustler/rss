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
}