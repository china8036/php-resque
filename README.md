# php-resque

一 运行机制简介
1 php-resque 介绍
       php-resuqe 基本介绍请参阅   PHP的轻量消息队列php-resque使用说明

2 目录结构说明

── config 配置信息
│   ├── config.php //本地开发配置
│   ├── config.prod.php //线上配置
│   ├── config.test.php //测试站配置
│   └── crontab.php //定时任务配置
├── data //数据
│   ├── log //日志
│   └── sql // sql变动信息
├── index.php //入口文件 运行入口
├── jobs // 实际执行任务类的目录
│   ├── crontab //定时任务
│   ├── follow 
│   ├── goods
│   ├── member
│   ├── store
│   └── test
├── plugins //php-resque插件
│   ├── DbLog.php //尝试数据库记录运行记录的插件示例
│   └── FileLog.php //使用文件记录运行记录的插件示例
├── queue.php //投递任务 多用于测试
├── src //核心文件目录
│   ├── abst //抽象类
│   ├── Core.php //初始化配置 自动加载 异常扑捉  
│   ├── Crontab.php // 定时监控逻辑
│   ├── DB.php //数据库链接相关
│   ├── intface //接口
│   ├── Log.php //日志记录类
│   └── Work.php //队列执行和任务分配类
└── vendor //第三方类库 （目录结构改造过）
    ├── Credis //redis 链接相关
    ├── Psr // psr 日志类（ClassLoader为开发时候放入非官方）
    ├── Resque // resuqe官方sdk
    ├── Resque.php
    ├── Slim //PDO 数据库链接相关
    └── TimeCheer // 团队自主 vendor
  3 jobs开发说明
          php-resque会自动执行投递任务的名称同名类的perform方法 因此每个job类必须实现perform方法 

          php-resque任务投递的参数自动附加到 $this->args里 请自行获取

          如果类中含有setUp 则此方法会在perform执行前执行此方法

          如果类中含有 tearDown 方法 则perform运行后执行此方法

          如需请求内部OAuth2 Api 接口请继承OAOJobBase

          演示示例 ：jobs\test\Demo.php   
