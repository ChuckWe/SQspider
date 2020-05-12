<?php
return [
    'setting' => [
        'worker_num'     	=> 4, //开启任务数
        'daemonize'      	=> 0,
        'max_request'     	=> 1000,
        'dispatch_mode'    	=> 2,//进程数据包分配模式 1平均分配，2按FD取摸固定分配，3抢占式分配
        'log_file'      	=> HOME_PATH . "/logs/swoole.log" ,
        'task_worker_num'   => 10, //任务处理数目
        'open_eof_check'    => true, //是否检测结尾
        'package_eof'       => PHP_EOL, //结尾标识，这里的结尾最好使用不容易跟真正的body混淆的字符
        'open_eof_split'    => true //必须开启切割
    ],
    'config' => [
        'host' => '127.0.0.1',
        'port' => 5389,
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
        'redisHost' => '127.0.0.1',
        'redisPort' => 6379
    ]
];