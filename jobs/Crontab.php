<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */
namespace jobs;

use core\Log;
use core\intface\Job;
class Crontab implements Job
{

    public function perform()
    {
        Log::record('crontab_jobs', [$this->args]);
    }

}
