<?php
namespace core;

class SortSpider
{
    public static $instance = [];

    public static function push($spider)
    {
        self::$instance[] = $spider;
    }

    public static function runAll()
    {
        logInfo('开始实例化队列');
        global $obj;
        global $config;

        $obj = $config['store'];
        /**
         * @var \core\store\StoreInterface
         */
        $obj = new $obj($config);

        foreach (self::$instance as $item)
        {
            (new $item())->run();
        }
    }
}