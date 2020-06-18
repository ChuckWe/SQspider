<?php
namespace core;

use QL\QueryList;
use QL\Ext\PhantomJs;
use QL\Ext\CurlMulti;
class QList
{
    private static $instance;

    public static $querylist;

    public $url = '';

    public $method = 'GET';

    public $typeUrl = 1;

    public static $user_agent = [
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

    private function __construct()
    {
        self::$querylist = QueryList::getInstance();
        $this->initQueryList();
    }

    private function __clone()
    {
    }

    public static function getInstance()
    {
        if(!self::$instance){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function initQueryList()
    {
        self::$querylist->use(PhantomJs::class,HOME_PATH.'/phantomjs/bin/phantomjs.exe');
        self::$querylist->use(CurlMulti::class);

    }

    public static function setting()
    {
        return [
            'headers' => ['User-Agent'=>self::$user_agent[array_rand(self::$user_agent)]],
        ];
    }

    public function result()
    {
        $method = $this->method;
        $url = $this->url;
        if($this->typeUrl == 1){
            return self::$querylist->$method($url,null,self::setting());
        }else{
            return self::$querylist->browser(function (\JonnyW\PhantomJs\Http\RequestInterface $r) use ($url,$method){
                $r->setMethod(strtoupper($method));
                $r->setUrl($url);
                $r->setTimeout(10000); // 10 seconds
                $r->addHeaders(self::setting());
                // $r->setDelay(1); // 3 seconds
                return $r;
            });
        }
    }

    public function __call(String $name,Array $arguments)
    {
        return self::$querylist->$name(...$arguments);
    }

}