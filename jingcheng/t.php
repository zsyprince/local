#!/usr/bin/php7
<?php
$parentPid = posix_getpid();
echo "parent progress pid:{$parentPid}\n";$childList = array();
// 3个生产者子进程
for ($i = 0; $i < 3; $i ++ ) {
    $pid = pcntl_fork();
    if ( $pid == -1) {
        // 创建失败
        exit("fork progress error!\n");
    } else if ($pid == 0) {
        sleep(rand(0,6));
        // 子进程执行程序
        $pid = posix_getpid();
        exit("({$pid})child progress end!\n");
    }else{
        // 父进程执行程序
    }
    $childList[$pid] = 1;
    echo "create producer child progress: {$pid} \n";
}
for ($i = 2; $i < 3; $i ++ ) {
    pcntl_wait($status);
}
//
echo "({$parentPid})main progress end!\n";
