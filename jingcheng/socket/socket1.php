#!/usr/bin/php7
<?php
//老规矩，创建一个socket流
$socket = stream_socket_server("tcp://0.0.0.0:8001");

//初始化一个event_base对象
$base = event_base_new();
//初始化一个事件
$event = event_new();

/**
 * event_set
 * 设置这个事件的触发条件
 * EV_READ 表示FD就绪，可以读取的时候 ，事件成为激活状态
 * EV_PERSIST 表示事件是持久的
 * 既当fd可读取时，事件将会执行ev_accept函数
 */
event_set($event, $socket, EV_READ | EV_PERSIST, 'ev_accept', array($event, $base));
//将指定事件和base对象关联起来
event_base_set($event, $base);
//将事件追加到当前base对象的事件队列
event_add($event);
//处理事件循环
event_base_loop($base);

$sockets = [];

/**
 * 处理客户端向服务端write的事件
 * 因为read也是个阻塞过程，所以也用event的方式来处理
 *
 * @param $socket
 * @param $event
 * @param $args
 */
function ev_accept($socket, $event, $args)
{
    global $sockets;
    $base = $args[1];
    $connection = stream_socket_accept($socket);
    stream_set_blocking($connection, 0);
    $sockets[] = $connection;
    $ev_read = event_new();
    event_set($ev_read, $connection, EV_READ | EV_PERSIST, 'ev_read', array($ev_read, $base));
    event_base_set($ev_read, $base);
    event_add($ev_read);
}

/**
 * 把消息转发到其他的fd上
 *
 * @param $connection
 * @param $event
 * @param $args
 */
function ev_read($connection, $event, $args)
{
    global $sockets;
    $read = stream_socket_recvfrom($connection, 1024);
    foreach ($sockets as $socket) {
        if ($socket != $connection) {
            fwrite($socket, $read);
        }
    }
}
