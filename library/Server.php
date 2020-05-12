<?php
namespace library;

use Swoole\Server as swool_server;
use Swoole\Client as swool_client;
use Swoole\Timer;

class Server
{
    private static $setting = [
        'worker_num'     	=> 4, #开启任务数
        'daemonize'      	=> 0,
        'max_request'     	=> 1000,
        'dispatch_mode'    	=> 2,//进程数据包分配模式 1平均分配，2按FD取摸固定分配，3抢占式分配
        'log_file'      	=> HOME_PATH . "/logs/swoole.log" ,
        'task_worker_num'   => 4,
    ];

    private static $config = [
        'host' => '127.0.0.1',
        'port' => 5489,
        'mode' => SWOOLE_PROCESS,
        'sockType' => SWOOLE_SOCK_TCP,
        'redisHost' => '127.0.0.1',
        'redisPort' => 6379

    ];

    private $serv;

    public static $redis = NULL;

    public static $rules;

    public function __construct($config = [])
    {
        global $_RULES;
        if(isset($config['config'])){
            self::$config = array_merge(self::$config,$config['config']);
        }
        $this->serv = new swool_server(
            self::$config['host'],
            self::$config['port'],
            self::$config['mode'],
            self::$config['sockType']
        );
        if(isset($config['setting'])){
            self::$setting = array_merge(self::$setting,$config['setting']);
        }
        self::$rules = $_RULES;
        $this->serv->set(self::$setting);
        $this->serv->on('Receive',		array($this,'onReceive'));
        $this->serv->on('WorkerStart',	array($this,'onWorkerStart'));
        $this->serv->on('WorkerStop',	array($this,'onWorkerStop'));
        $this->serv->on('ManagerStart', function ($serv) {
            echo '管理进程开启-------'.PHP_EOL;
        });
        $this->serv->on('Task',     array($this,'onTask'));
        $this->serv->on('Finish',     array($this,'onFinish'));
        $this->serv->on('Close',    array($this,'onClose'));
        $this->serv->start();
    }

    public function onWorkerStart($serv, $worker_id)
    {
        if(self::$redis === NULL){
            try{
                self::$redis = new \Redis();
                self::$redis->connect(self::$config['redisHost'], self::$config['redisPort']);
                self::$redis->select(3);
                self::Log("Connect redis on {$serv->worker_id} at ".date('Y-m-d H:i:s'));
            }catch (\Exception $e){
                echo "redis Error for connect on {$worker_id} at ".date('Y-m-d H:i:s').PHP_EOL;
                self::Log("redis Error for connect on {$worker_id} at ".date('Y-m-d H:i:s').PHP_EOL);
            }

        }
        self::Log("worker {$worker_id} 开始执行等待 ".date('Y-m-d H:i:s'));
        if($worker_id === 0){
            $rules = self::$rules;
            Timer::after(3000,function() use ($rules){
                echo "-------------------开始执行爬取任务-----------------\n";
                $client = new swool_client(SWOOLE_SOCK_TCP);
                if (!$client->connect(self::$config['host'], self::$config['port'], -1)) {
                    exit("connect failed. Error: {$client->errCode}\n");
                }
                $client->send($rules['commonReceive']."\n");
                $client->close();
            });
        }
    }

    public function onWorkerStop($serv, $worker_id)
    {
        if(self::$redis){
            self::$redis->close();
            self::$redis=NULL;
        }
        self::Log("unLine redis on {$worker_id} at ".date('Y-m-d H:i:s'));
    }

    public function onReceive(swool_server $serv, $fd, $reactoriId, $data)
    {
        $isRight = strpos($data,'::');
        if($isRight === false){
            echo "内容不合规格,ip:{$serv->getClientInfo($fd)['remote_ip']}\n";
            $serv->send($fd,"内容不合规格,ip:{$serv->getClientInfo($fd)['remote_ip']}\n");
            return;
        }

        $dataInfo = explode('::',$data);
        if(count($dataInfo) != 3){
            echo "参数不合规格,ip:{$serv->getClientInfo($fd)['remote_ip']}\n";
            $serv->send($fd,"参数不合规格,ip:{$serv->getClientInfo($fd)['remote_ip']}\n");
            return;
        }

        try {
            $obj = '\\app\\receive\\'.$dataInfo[0];
            $app = new $obj;
            $app->serv = $serv;
            $app->fd = $fd;
            $app->redis = self::$redis;
            $app->data = trim($dataInfo[2]);
            $app->rules = self::$rules;
            $function = (string)$dataInfo[1];
            $app->$function();
        }
        catch (\Exception $e) {
            echo $e;
            Server::Log("[Received Failed]:" . $e->getMessage()."\n");
            return;
        }

    }

    public function onTask(swool_server $serv, $task_id, $src_worker_id, $data)
    {
        echo "任务:{$data}\n";
        $dataInfo = explode('::',$data);
        try {
            $obj = '\\app\\task\\'.$dataInfo[0];
            $app = new $obj;
            $app->redis = self::$redis;
            $app->serv = $serv;
            $app->task_id = $task_id;
            $app->data = trim($dataInfo[2]);
            $app->rules = self::$rules;
            $function = (string)$dataInfo[1];
            $app->$function();
        }
        catch (Exception $e) {
            echo $e;
            Server::Log("[Task Failed]:" . $e->getMessage()."\n");
            return;
        }
    }

    public function onFinish(swool_server $serv, $task_id, $data)
    {
        if($data === 'oneFinish'){
            echo self::$rules[$data].PHP_EOL;
            return;
        }
        $client = new swool_client(SWOOLE_SOCK_TCP);
        if (!$client->connect(self::$config['host'], self::$config['port'], -1)) {
            exit("connect failed. Error: {$client->errCode}\n");
        }
        try{
            $dataInfo = explode('::',$data);
            $client->send(self::$rules[$dataInfo[0]].$dataInfo[1]."\n");
            $client->close();
        }catch (\Exception $e){
            echo $e;
            Server::Log("[Receive Failed]:" . $e->getMessage()."\n");
            return;
        }
    }

    public function onClose($serv, $fd, $reactorId)
    {
        // echo "关闭=>通讯者:#{$reactorId},客户端:{$fd}\n";
    }

    public static function Log($data)
    {
        $file = HOME_PATH . "/logs/" . date('Ymd') . ".log";
        file_put_contents($file, $data . "\r\n", FILE_APPEND | LOCK_EX);
    }
}