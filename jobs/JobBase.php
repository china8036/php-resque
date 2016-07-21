<?php

/**
 * @copyright (c) 2916, Ryan [CHAOMA.ME]
 */

namespace jobs;

use core\intface\Job;

abstract class JobBase implements Job
{

    /**
     * 错误码
     * @var type 
     */
    protected $errId = null;

    /**
     * 错误信息
     * @var string 
     */
    protected $errMSg = '';

    /**
     * 得到错误id
     * @return int 
     */
    public function getErrorId()
    {
        return $this->errId;
    }

    /**
     * 得到错误信息
     * @return sting
     */
    public function getErrorMsg()
    {
        return $this->errMSg;
    }
    
    /**
     *得到错误信息
     */
    public function getError(){
        return [$this->errId, $this->errMSg];
    }

}
