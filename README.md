# php-resque

        主要进一步封装php-resque https://github.com/chrisboulton/

##配置文档
    
       config/config.prod.php -> config/config.php
##定时任务
      config/crontab.php
               <code>
               return array(
                 '5' => array('jobs/test/Demo', 'jobs/crontab/Demo'), //5秒定时任务
                 '*-09 16:00:00' => array('jobs/test/Demo'),//每个月9号 小时 分钟
                 '1 16:01:00' => array('jobs/test/Demo'),//每周一 小时 分钟
                 );
               </code>
##运行 
       php index.php
