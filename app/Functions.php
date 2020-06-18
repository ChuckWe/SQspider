<?php
function logInfo($msg)
{
    $msg = "[info]".date('Y-m-d H:i:s',time())."{".$msg."}"."[info]";
    file_put_contents(log_exit(), $msg . "\n", FILE_APPEND|LOCK_EX);
}

function log_exit()
{
    if(!file_exists(HOME_PATH . "/logs/".date('Y-m-d',time()).".log")){
        @mkdir(HOME_PATH . "/logs/",0755,true);
        @file_put_contents(HOME_PATH . "/logs/".date('Y-m-d',time()).".log", '日志已生成' . "\n", FILE_APPEND|LOCK_EX);
    }
    return HOME_PATH . "/logs/".date('Y-m-d',time()).".log";
}