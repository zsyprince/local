#!/usr/bin/php7
<?php
$message_queue_key = ftok(__FILE__, 'a'); 
$message_queue = msg_get_queue($message_queue_key, 0666); 
$pids = array(); 

for ($i = 0; $i < 5; $i++) { 
    //创建子进程 
    $pids[$i] = pcntl_fork(); 

    if ($pids[$i]) { 
        echo "No.$i child process was created, the pid is $pids[$i]\r\n"; 
    } elseif ($pids[$i] == 0) { 
        $pid = posix_getpid(); 
        echo "process.$pid is writing now\r\n"; 
        msg_send($message_queue, 1, "this is process.$pid's data\r\n"); 
        posix_kill($pid, SIGTERM); 
    } 
} 

do { 
    msg_receive($message_queue, 0, $message_type, 1024, $message, true, MSG_IPC_NOWAIT); 
    echo $message; 
    //需要判断队列是否为空，如果为空就退出 
    //break; 
} while(true); 
