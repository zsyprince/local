#!/usr/bin/php7
<?php
class fork
{
    public $forkNum = 1;
    public $worker  = null;
    public $pid     = null;

    public function __construct()
    {
        $this->pid = posix_getpid();
    }

    private function fork()
    {
        if ($this->forkNum > 0) {
            for ($i = 0; $i < $this->forkNum; $i++) {
                $pid = pcntl_fork();
                if ($pid == 0) {
                    cli_set_process_title('work_test');
                    $this->worker->work($i);
                    exit;
                } elseif($pid) {
                    cli_set_process_title('master_test');
                } else {
                    exit('fork error!!');
                }
            }

        }
    }

    private function wait()
    {
        for ($i = 0; $i < $this->forkNum; $i++) {
            pcntl_wait($status);
        }
        exit("main progress end!\n");
    }

    public function run($forkNum,iworker $worker)
    {
        $this->forkNum  = $forkNum;
        $this->worker   = $worker;
        $this->fork();
        $this->wait();
    }

    public function __destruct()
    {

    }
}

interface iworker
{
    public function work($i);
}

class worker implements iworker
{
    public function __construct()
    {

    }

    public function work($i )
    {
        echo "$i hello world!!!\n";
        sleep(1);
    }

}

$fork = new fork();
$forkNum = 5;
$fork->run($forkNum,(new worker()));