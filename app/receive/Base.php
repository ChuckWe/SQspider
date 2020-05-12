<?php
namespace app\receive;

class Base{
    /**
     * @var swool_server
     */
    public $serv;

    /**
     * @var 传入参数
     */
    public $data;

    /**
     * @var 客户端id
     */
    public $fd;

    /**
     * @var redis连接
     */
    public $redis;
    /**
     * @var 爬虫规则json文件
     */
    public $rules;

    public function __construct()
    {
        global $_RULES;
        $this->rules = $_RULES;
        $this->_init();
    }

    public function _init()
    {

    }

    public function run()
    {
        foreach ($this->rules['urls'] as $url){
            $taskReceive = $this->rules['commonTask'].$url;
            $this->serv->task($taskReceive);
            echo "正在爬取{$url}\n";
        }
    }

}