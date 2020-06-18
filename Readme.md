##### 请安装swoole扩展
# SQspider
>git clone https://github.com/ChuckWe/SQspider.git  
>cd SQspider  
>composer install  
>\#创建爬虫  
>php sqspider start create:test  
>\#执行爬虫阵列  
>php sqspider start runSq:sort

### 如果创建爬虫名称不是test需更改app下面app.php的sortSpider  

~~~
return [
    'console' => [
        'meta' => [
            'name' => 'SQspider App',
            'version' => '1.0.0'
        ],
        'commands' => [
            'start' => commands\Start::class
        ]
    ],
    'store' => other\StoreWithObj::class,
    // 'store' => other\StoreWithRedis::class,
    'sortSpider' => [
        spider\test::class,
    ],
    'redis' => [
        'host' => '172.16.1.40',
        'port' => '6379',
        'prefix'  => 'php:spider:'
    ],
];
~~~
>注意:sortSpider是编排爬虫阵列,如果你需要爬取的数据是多层的。那么需要创建多个爬虫按照顺序放入sortSpider。可直接在爬虫文件内部使用swoole协程  
>
>默认存储方式是内存共享,可设置为redis
>~~~
>只需要在爬虫内部
>global $obj
>即可使用
>$obj->add($key,$data)
>$obj->get($key)
>$obj->del($key)
>~~~

## 具体爬虫逻辑请参照QueryList
在爬虫内部getRuslt()返回的便是QueryList实例  
直接使用QeuryList方法便可
