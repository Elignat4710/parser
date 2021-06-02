<?php

namespace App\Classes;

use Predis\Client;
use Predis\Connection\ConnectionException;

/**
 * Class for Redis
 */
class Redis
{
    // Redis instance
    protected $redis;

    /**
     * Connecting to redis
     */
    public function __construct()
    {
        try {
            $this->redis = new Client([
                'host' => env('REDIS_HOST', '127.0.0.1'),
                'port' => env('REDIS_PORT', 6379),
                'async' => true,
                'read_write_timeout' => 0
            ]);
        } catch (ConnectionException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Breaking the connection
     */
    public function __destruct()
    {
        $this->redis = null;
    }

    public static function init()
    {
        return new self();
    }
    
    /**
     * Cleaning redis fully
     */
    public function flushall()
    {
        return $this->redis->flushall();
    }

    /**
     * Moving to the end of queue
     */
    public function rpush($key, $value)
    {
        return $this->redis->rpush($key, $value);
    }

    /**
     * Pulling the record or blocking
     */
    public function blpop($key, $index = 0)
    {
        return $this->redis->blpop($key, $index)[1];
    }

    /**
     * Checking for records
     */
    public function exists($key)
    {
        return $this->redis->exists($key);
    }

    /**
     * Grabbing the record from the beginning of the list
     */
    public function lpop($key)
    {
        return $this->redis->lpop($key);
    }
}
