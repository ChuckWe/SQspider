<?php


namespace app\receive;


class commonReceive extends Base
{

    public function run()
    {
        foreach ($this->rules['urls'] as $url){
            $taskReceive = $this->rules['commonTask'].$url;
            $this->serv->task($taskReceive);
            echo "正在爬取{$url}\n";
        }
    }
}