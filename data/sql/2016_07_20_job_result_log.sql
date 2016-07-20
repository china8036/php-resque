CREATE TABLE `job_result_log` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `job_id` char(50) not null default '' comment '任务标示id 发送失败无此数值',
  `job_name` char(30) DEFAULT '' NOT NULL COMMENT '任务名称',
  `params` text  COMMENT '传入的参数params',
  `queue` char(30) DEFAULT '' NOT NULL COMMENT '队列名称',
  `result` text COMMENT '返回数据',
  `flag` tinyint(1) not null default 0 COMMENT '成功与否 1成功 0失败',
  `added_time` timestamp NOT NULL  DEFAULT CURRENT_TIMESTAMP COMMENT '添加时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COMMENT='运行记录表';
