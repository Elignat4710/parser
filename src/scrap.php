<?php

use App\Classes\MySql;
use App\Classes\Queue;
use App\Classes\Redis;

require_once __DIR__ . '/bootstrap.php';

// Setting up forks
pcntl_async_signals(true);

    pcntl_signal(SIGTERM, 'signalHandler');
    pcntl_signal(SIGHUP, 'signalHandler');
    pcntl_signal(SIGINT, 'signalHandler');

// Saving parent pid
file_put_contents('parentPid.out', getmypid());

// Scraper initialization
if (isset($argv[1]) && $argv[1] == 'init') {
    echo "Init ...\n";
    
    $redis = Redis::init();
    $redis->flushall();

    $zipcodes = file_get_contents(__DIR__ . '/zipcode.json'); // Reading zipcodes from file
    $zipcodes = json_decode($zipcodes);

    // Passing initial tasks into task queue
    foreach ($zipcodes as $zipcode) {
        $link = 'https://hotpads.com/' . $zipcode . '/apartments-for-rent';
        $redis->rpush('tasks', json_encode([
            'link' => $link,
            'class' => 'App\\Classes\\PaginateScrap'
        ]));
    }
} elseif (isset($argv[1]) && $argv[1] == 'check') { // Giving scraper the task to check records relevance
    echo "Check all records\n";
    $redis = Redis::init();
    $db = new MySql;

    // Getting all records from the db for checking
    $allRecords = $db->getAllRecordsDate('properties');
    // Adding records to queue
    foreach ($allRecords as $record) {
        $redis->rpush('tasks', json_encode([
            'class' => '\\App\\Classes\\CheckLinks',
            'link' => $record->link
        ]));
    }
}

// Scraping start
echo "Scrap ...\n";

$queue = new Queue;
$queue->start();

// Stop signals handler
function signalHandler($signal)
{
    global $queue;
    unset($queue);
    exit;
}
