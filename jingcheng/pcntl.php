#!/usr/bin/php7
<?php
$parentPid = posix_getpid();
echo "parent progress pid:{$parentPid}\n";
$childList = array();
$pid = pcntl_fork();
if ( $pid == -1) {
    // 创建失败
    exit("fork progress error!\n");
} else if ($pid == 0) {
    $sid = posix_setsid();
    if ($sid < 0) {
        echo 'setsid error';
        exit;
    }
    // 子进程执行程序
    $pid = posix_getpid();
sleep(5);
     echo "({$pid})child progress is running!  \n";
    exit("({$pid})child progress end!\n");
} else {
    // 父进程执行程序
    $childList[$pid] = 1;
}
// 等待子进程结束
pcntl_wait($status);
echo "({$parentPid})main progress end!!! \n";
