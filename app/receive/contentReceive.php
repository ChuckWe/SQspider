<?php


namespace app\receive;


class contentReceive extends Base
{
    public function run()
    {
        $data = [];
        while($url = $this->redis->rPop('urls')){
            $data[] = $this->rules[$this->data].$url;
        }
        $result = $this->serv->taskCo($data,10.0);
        print_r($result);
        echo "-----------------------所有任务已完毕--------------------------\n";
    }
}