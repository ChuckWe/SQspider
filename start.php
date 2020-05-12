<?php
date_default_timezone_set('PRC');
define('HOME_PATH', dirname(__FILE__));

use library\Server;

require(HOME_PATH . '/vendor/autoload.php');
require(HOME_PATH . '/core/autoload.php');
$config = include(HOME_PATH . '/app/config.php');

if(!file_exists('logs')){
    mkdir('logs',0777,true);
}

if($argc !== 2){
    echo "[param error]:rule error\n";
    exit;
}

if(!file_exists('./rules/'.$argv[1].'.json')){
    echo "[param error]:rule file is null\n";
    exit;
}

$_RULES = json_decode(file_get_contents('./rules/'.$argv[1].'.json'),true);

try{
    $run = new Server($config);
}catch(Exception $e){
    echo "[Start Failed]:" . $e->getMessage()."\n";
    Server::Log("[Start Failed]:" . $e->getMessage()."\n");
}
