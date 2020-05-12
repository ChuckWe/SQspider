<?php


namespace library;
use QL\QueryList;
use QL\Ext\PhantomJs;
use QL\Ext\CurlMulti;

class Query
{
    private static $ql;
    private static $queryList;

    public  $urlType = 1;

    public  $setting;

    public  $method = 'get';

    public static function getInstance()
    {
        if(is_null(self::$ql)){
            self::$ql = new self;
        }
        return self::$ql;
    }

    public static function html($url)
    {
        if(is_null(self::$ql)){
            self::getInstance();
        }
        self::$queryList = QueryList::getInstance();
        self::$queryList->use(PhantomJs::class,HOME_PATH.'/phantomjs/bin/phantomjs');
        self::$queryList->use(CurlMulti::class);
        $method = self::$ql->method;
        if(self::$ql->urlType){
            return self::$queryList->$method($url,null,self::$ql->setting);
        }else{
            return self::$queryList->browser(function (\JonnyW\PhantomJs\Http\RequestInterface $r) use ($url,$method){
                $r->setMethod(strtoupper($method));
                $r->setUrl($url);
                $r->setTimeout(10000); // 10 seconds
//            $r->setDelay(1); // 3 seconds
                return $r;
            });
        }
    }

}