<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */
use core\Log;

class Crontab implements core\intface\Job
{

    public function perform()
    {
        Log::record('crontab_jobs', [$this->args]);
    }

}
