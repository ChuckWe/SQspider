<?php

require dirname(__DIR__) . '/vendor/autoload.php';

spl_autoload_register(function ($class) {
    if ($class) {
        $file = str_replace('\\', '/', $class);
        $file = HOME_PATH . '/' . $file . '.php';
        if (file_exists($file)) {
            include $file;
        } else {
            if(!file_exists(HOME_PATH . "/logs/autoload.log")){
                @mkdir(HOME_PATH . "/logs/",0755,true);
            }
            file_put_contents(HOME_PATH . "/logs/autoload.log", $file . "\n", FILE_APPEND|LOCK_EX);
        }
    }
});
$config = require dirname(__DIR__) . '/app/app.php';
