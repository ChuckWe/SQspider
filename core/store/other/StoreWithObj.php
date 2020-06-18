<?php
namespace core\store\other;

use core\store\StoreInterface;
class StoreWithObj implements StoreInterface
{

    public $obj;

    public function __construct($config)
    {
        logInfo('存储方式---内存');
        $this->obj = [];
    }

    public function add($key,$data)
    {
        $this->obj[$key] = $data;
    }

    public function del($key)
    {
        unset($this->obj[$key]);
    }

    public function get($key='')
    {
        if(empty($key)){
            return $this->obj;
        }
        return $this->obj[$key];
    }

    public function getKey($data)
    {
        foreach ($this->obj as $key => $item)
        {
            if($item === $data){
                return $key;
            }
        }
        return '';
    }
}