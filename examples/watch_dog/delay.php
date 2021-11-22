<?php

declare(strict_types = 1);

use Swow\Coroutine;
use Swow\Sync\WaitReference;
use Swow\WatchDog;

WatchDog::run(100 * 1000 * 1000, 0, function ()
{
    //3秒后退出协程
    sleep(3);
    echo "sleee time 3\n";
    exit;
});

$wf    = new WaitReference();
$count = 0;
Coroutine::run(function () use ($wf, &$count)
{
    while (true) {
        $count++;
    }
});
WaitReference::wait($wf);
echo "Count:{$count} \n";
echo "Exited\n";
