<?php

use Swow\Coroutine;
use Swow\Sync\WaitReference;


$wr1 = new WaitReference();
Coroutine::run(static function () use($wr1): void {
    $wr = new WaitReference();
    echo "start connect\r\n";
    $session = ssh2_connect("192.168.7.240", 22);
    echo "connect done!\r\n";

    Coroutine::run(static function () use ($session, $wr): void {
        echo "start auth\r\n";
        $flag = ssh2_auth_password($session, 'root', 'x');
        echo "auth done\r\n";

        Coroutine::run(static function () use ($session,$wr): void {
            // exec command
            echo "Exec Command : sleep 5 && echo 'done 1!' \r\n";
            ssh2_exec($session, "sleep 5 && echo 'done 1!'");
    
            echo "Exec Command : sleep 5 && echo 'done 2!' \r\n";
            ssh2_exec($session, "sleep 5 && echo 'done 2!'");
        });
    });
    echo "auth 挂起\r\n";
    WaitReference::wait($wr);
    var_dump('done');
});

echo "connect 挂起\r\n";

WaitReference::wait($wr1);

echo "整体结束\r\n";