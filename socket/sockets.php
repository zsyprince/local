<?php
$serv = stream_socket_server("tcp://0.0.0.0:8000", $errno, $errstr) or die("create server failed");

for($i = 0; $i < 1; $i++) {
    if (pcntl_fork() ==0) {
        while(1) {
            $conn = stream_socket_accept($serv, 120);
            if ($conn == false) continue;
            $request = fread($conn,1024);
            fwrite($conn, $request, 1024);
            fclose($conn);
        }

        exit(0);
    }
}
fclose($serv);
for($i = 0; $i < 1;$i ++) {
    pcntl_wait($status);
}
