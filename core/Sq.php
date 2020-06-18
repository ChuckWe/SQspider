<?php
namespace core;

use core\QList;
class Sq
{
    /**
     * @var string 爬取链接
     */
    public $domain = '';
    /**
     * @var int 访问方式,1是普通请求,2需要photomjs
     */
    public $typeUrl = 1;
    /**
     * @var string 访问方式get/post
     */
    public $method = 'get';

    /**
     * @var \core\QList querylist实例
     */
    public $sq;

    public function __construct()
    {
        $this->sq = QList::getInstance();
    }

    /**
     * @return mixed 获取结果返回querylist实例
     */
    public function getRuslt()
    {
        $this->sq->url = $this->domain;
        $this->sq->typeUrl = $this->typeUrl;
        $this->sq->method = $this->method;
        return $this->sq->result();
    }
}