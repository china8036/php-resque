<?php

namespace core\intface;

/**
 * @copyright (c) 2016, Ryan [CHAOMA.ME]
 * worker限制必须提供perform方法
 */

interface Worker
{

    public function perform();
}
