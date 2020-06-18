<?php
namespace app\commands;

use core\SortSpider;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Start extends Command
{
    protected static $defaultName = 'start';

    protected $input;

    protected $output;

    protected $name;

    protected function configure()
    {
        $this->setName('start')
            ->setDescription('开始爬虫')
            ->setHelp("开始爬虫");

        $this->addArgument('options', InputArgument::REQUIRED, '创建爬虫create:name,运行爬虫runSq:name');

    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $spiderName = $input->getArgument('options');
        if(strpos($spiderName,':') === false){
            $output->writeln("<error>参数错误</error>");
        }
        $arop = explode(':',$spiderName);
        if(count($arop)<2 || empty($arop[0]) || empty($arop[1])){
            $output->writeln("<error>参数错误</error>");
        }
        $this->input = $input;
        $this->output = $output;
        $function = $arop[0];
        $this->name = $arop[1];
        $this->$function($arop);
    }

    private function create($arop)
    {
        try{
            logInfo('创建命令');
            $this->output->writeln("<info>正在创建爬虫文件......</info>");
            if(!file_exists(HOME_PATH.'/core/common/example.php')){
                throw new \Exception($this->output->writeln("<error>无可用案例文件</error>"));
            }
            if(!file_exists(HOME_PATH.'/app/spider/')){
                @mkdir(HOME_PATH.'/app/spider/',0755,true);
            }
            if(!file_exists(HOME_PATH.'/app/spider/'.$this->name.'.php')){
                $phpstr = file_get_contents(HOME_PATH.'/core/common/example.php');
                $phpstr = str_replace('example',$this->name,$phpstr);
                file_put_contents(HOME_PATH.'/app/spider/'.$this->name.'.php',$phpstr);
                $this->output->writeln("<info>创建爬虫文件{$this->name}成功</info>");
            }else{
                throw new \Exception($this->output->writeln("<error>已存在爬虫文件{$this->name}</error>"));
            }
        }catch (\Exception $e){
            if($e->getMessage() !== 'swoole exit'){
                print_r($e->getMessage());
            }
        }
    }

    private function runSq($arop)
    {
        try{
            logInfo('执行爬虫队列');
            $this->output->writeln("<info>参数已收到</info>");
            if($this->name != 'sort'){
                throw new \Exception($this->output->writeln("<error>请用runSq:sort命令开始爬虫队列</error>"));
            }
            global $config;
            foreach ($config['sortSpider'] as $i){
                if(!class_exists($i)){
                    throw new \Exception($this->output->writeln("<error>app设置错误,爬虫不存在[{$i}]</error>"));
                }
            }
            SortSpider::runAll();
        }catch (\Exception $e){
            if($e->getMessage() !== 'swoole exit'){
                print_r($e->getMessage());
            }
        }
    }

    public function __call( String $name , Array $arguments)
    {
        $this->output->writeln("<error>没有可执行方法</error>");
    }
}