<?php


namespace app\task;



class commonTask extends TaskBase
{

    public function _init()
    {
        $this->htmlRange = '.item';
        $this->htmlRules = [
            'url' => ['td:eq(0)>a','href']
        ];
    }

    public function queryData($data)
    {
        foreach ($data as $key => $item){
            $this->redis->lPush('urls',$item['url']);
        }
        $this->serv->finish('contentReceive::contentTask');
    }
}