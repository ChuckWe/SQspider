<?php
namespace core\store;

interface StoreInterface
{
    public function add($key,$data);
    public function del($key);
    public function get($key);
    public function getKey($data);
}