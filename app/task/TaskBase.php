<?php
namespace app\task;

use library\Query;

class TaskBase{
    /**
     * @var swool_server
     */
    public $serv;

    /**
     * @var 传入参数
     */
    public $data = [];

    /**
     * @var 任务id
     */
    public $task_id;

    /**
     * @var redis连接
     */
    public $redis;
    /**
     * @var 爬虫规则json文件
     */
    public $rules;

    /**
     * @var array 爬取网页,可自定义
     */
    public $urls = [];

    /**
     * @var int 类型js动态加载网页2,普通爬取1
     */
    public $urlType = 1;

    /**
     * @var 网页规则具体请参照QueryList css选择
     */

    public $htmlRules;

    /**
     * @var 网页爬取区域具体请参照QueryList range区域
     */
    public $htmlRange;

    /**
     * @var array 浏览器附带设定,目前只携带了User-Agent
     */
    public $setting;

    /**
     * @var Query QueryList实例
     */
    public $ql;


    public function __construct()
    {
        $this->urls = $this->rules['urls'];
        $this->setting = $this->setting();
        $this->ql = Query::getInstance();
        $this->ql->urlType = $this->urlType;
        $this->ql->setting = $this->setting;
        $this->_init();
    }

    /**
     * 子类加载基础参数
     */
    public function _init()
    {

    }

    /**
     * 执行QueryList爬取方法,子类覆盖
     */
    public function run()
    {
        $queryData = $this->ql->html($this->data)
            ->rules($this->htmlRules)
            ->range($this->htmlRange)
            ->queryData();
        $this->queryData($queryData);
    }

    /**
     * 获取结果方法,子类覆盖
     */
    public function queryData($data)
    {
        $file = HOME_PATH . "/logs/" . $this->rules['file'];
        foreach ($data as $key => $item){
            $str = '';
            foreach ($item as $value) {
                $str .= $value . "\n";
                echo $value . "\n";
            }
            @file_put_contents($file, $str . "\r\n", FILE_APPEND | LOCK_EX);
        }
    }

    public static function setting()
    {
        $user_agent = [
            'Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/64.0.3282.186 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/51.0.2704.103 Safari/537.36',
            'Mozilla/5.0 (X11; Linux x86_64; rv:29.0) Gecko/20100101 Firefox/29.0',
            'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.9; rv:29.0) Gecko/20100101 Firefox/29.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_9_3) AppleWebKit/537.75.14 (KHTML, like Gecko) Version/7.0.3 Safari/537.75.14',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:29.0) Gecko/20100101 Firefox/29.0',
            'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/34.0.1847.137 Safari/537.36',
            'Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko',
        ];
        return [
            'headers' => ['User-Agent'=>$user_agent[array_rand($user_agent)]],
        ];
    }
}