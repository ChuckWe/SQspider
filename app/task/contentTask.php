<?php


namespace app\task;



class contentTask extends TaskBase
{

    public function _init()
    {
        $this->htmlRules = [
            'title' => ['#content > h1 > span:nth-child(1)','text'],
            'jianjie' => ['#link-report > span','text']
        ];
        $this->htmlRange = '';
    }

    public function queryData($data)
    {
        $file = HOME_PATH.'/down/';
        if(!file_exists($file)){
            @mkdir($file,0777,true);
        }
        $file = $file.$this->rules['file'];
        $str = '';
        $str .= $data['title']."\r\n";
        $str .= $data['jianjie']."\r\n";
        @file_put_contents($file, $str, FILE_APPEND | LOCK_EX);
        $this->serv->finish('oneFinish');
    }
}