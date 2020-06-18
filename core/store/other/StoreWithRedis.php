<?php


namespace core\store\other;


use core\store\StoreInterface;
use Swoole\Coroutine\Redis;

class StoreWithRedis implements StoreInterface
{
    public $prefix;
    /**
     * @var Redis
     */
    public static $redis;

    public function __construct($config)
    {
        logInfo('存储方式---Redis');
        self::$redis = new Redis();
        self::$redis->connect($config['redis']['host'],$config['redis']['port']);
        print_r("\033[0;36m\nredis已连接\n\033[0m");
        $this->prefix = $config['redis']['prefix'];
        defer(function(){
            print_r("\e[0;36m\nredis正在关闭\n\e[0m");
            self::$redis->close();
        });
    }

    public function add($key, $data)
    {
        self::$redis->hSet($this->prefix.'store',$key,json_encode($data));
    }

    public function del($key)
    {
        self::$redis->hDel($this->prefix.'store',$key);
    }

    public function get($key='')
    {
        if(empty($key)){
            return json_decode(self::$redis->hGetAll($this->prefix.'store'));
        }
        return json_decode(self::$redis->hGet($this->prefix.'store',$key));
    }

    public function getKey($data)
    {
        $allValues = self::$redis->hGetAll($this->prefix.'store');
        foreach ($allValues as $key => $item)
        {
            if($item === $data){
                return $key;
            }
        }
        return '';
    }
}