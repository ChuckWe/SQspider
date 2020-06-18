<?php
namespace core;

use Symfony\Component\Console\Application;
use app\commands;
class InitCommand
{

    public $config = [
        'meta' => [
            'name' => 'SQspider App',
            'version' => '1.0.0'
        ],
        'commands' => [
            'start' => commands\Start::class,
        ]
    ];
    public $commands;
    public $setting;
    public function __construct($config)
    {
        $this->setting = $config;
        $this->config = array_merge($this->config,$config['console']);
    }

    public function start()
    {
        logInfo('初始化命令');
        foreach ($this->setting['sortSpider'] as $item){
            logInfo('排列爬虫'.$item);
            SortSpider::push($item);
        }
        $this->getCommand();
        $application = new Application($this->config['meta']['name'], $this->config['meta']['version']);
        foreach ($this->commands as $key => $value){
            $application->add($value);
        }
        $application->run();
    }

    protected function getCommand()
    {
        if(count($this->config['commands']) > 0){
            foreach ($this->config['commands'] as $key => $value){
                $this->commands[$key] = new $value();
            }
        }else{
            echo "[error]:command error\n";
            exit;
        }
    }
}