<?php


/* @noinspection PhpUnreachableStatementInspection */

declare(strict_types=1);

use Swow\Coroutine;
use Swow\WatchDog;

/**
 * 每个进程只能允许一个WatchDog 运行。不能多个协程开启watchDog
 */
// 0.1s/t
WatchDog::run(1000 * 1000, 0, function () {
    $coroutine = Coroutine::getCurrent();
    $coroutine->__alert_count = ($coroutine->__alert_count ?? 0) + 1;
    echo 'CPU starvation occurred, suspend this coroutine...' . PHP_EOL;
    sleep(0);
    if ($coroutine->__alert_count > 5) {
        $coroutine->kill();
    }
});

Coroutine::run(function () {
    $s = microtime(true);
    for ($n = 5; $n--;) {
        echo 'Do something...' . PHP_EOL;
        usleep(1000);
    }
    $s = microtime(true) - $s;
    echo "Use {$s} ms" . PHP_EOL;
});
Coroutine::run(function (){
    //ERROR do not run
    WatchDog::run(1000 * 1000, 0, function () {
        $coroutine = Coroutine::getCurrent();
        $coroutine->__alert_count = ($coroutine->__alert_count ?? 0) + 1;
        echo 'CPU starvation occurred, suspend this coroutine...' . PHP_EOL;
        sleep(0);
        if ($coroutine->__alert_count > 5) {
            $coroutine->kill();
        }
    });
    $count = 0;
    try {
        while (true) {
            $count++;
        }
    } finally {
        echo "Count is {$count}" . PHP_EOL;
    }
});

