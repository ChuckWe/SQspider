<?php
/**
 * run方法执行,getRsult()获得qeurylist实例,然后可以用querylist所有方法
 */
namespace app\spider;

use core\Sq;

class example extends Sq{
    public $domain = 'https://www.baidu.com/';
    public $typeUrl = 1;
    public $method = 'get';
    public function run()
    {
        /* 保存位置 默认内存*/
        global $obj;
        $data = $this->getRuslt();
        $src = $data->find('#lg>img')->attr('src');
        $obj->add('test',$src);
        print_r($obj->get('test'));
    }
}