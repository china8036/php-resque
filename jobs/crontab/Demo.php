<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */

namespace jobs\crontab;

use core\Log;
use jobs\OAOJobBase;
class Demo extends OAOJobBase
{
    public function setUp(){
        
    }

    public function perform()
    {
        $res = $this->requestHttp('agent/ad/good_goods/query', []);
        Log::record('crontab_jobs', [$this->args, $res, $this->getErrorMsg()]);
    }

}
