<?php

namespace App\Classes;

use DiDom\Document;

class Queue
{
    // Max number of forks
    protected $limit;
    // Stop for cycle
    protected $stop = false;
    // An array with child pid
    protected $pid = [];
    // Defines the parent process
    protected bool $parent = true;
    
    public function __construct()
    {
        $this->limit = env('FORK_LIMIT', 10);
    }

    /**
     * Adding the tasks back to queue
     */
    public function __destruct()
    {
        if (true === $this->parent) {
            foreach ($this->pid as $pid => $task) {
                posix_kill($pid, SIGTERM);
                Redis::init()->rpush('tasks', json_encode([
                    'class' => $task['class'],
                    'link' => $task['link'],
                ]));
            }
        }
    }
    
    /**
     * Starting scraper and distributing tasks among forks
     */
    public function start()
    {
        var_dump('start');
        while (!$this->stop) {
            $this->checkPid();
            $task = json_decode(Redis::init()->blpop('tasks'), true);

            if ($this->limit > count($this->pid) && $task) {
                $this->fork($task);
            } elseif (count($this->pid) >= $this->limit && $task) {
                Redis::init()->rpush('tasks', json_encode($task));
                sleep(1);
            } elseif (!$task) {
                echo 'Complited!' . PHP_EOL;
                $this->stop = true;
            }
        }
    }

    /**
    * Creating the forms
    *
    * @param  array $task
    */
    protected function fork($task)
    {
        $pid = pcntl_fork();

        if (-1 === $pid) {
            die('Could not fork');
        }

        if ($pid) {
            $this->pid[$pid] = $task;
        } else {
            $this->parent = false;
            $this->scrap($task);
            exit();
        }
    }

    /**
     * Pulling the content and passing tasks to scrapers classes
     *
     * @param  array $task
     * @return bool
     */
    public function scrap($task)
    {
        $request = StormProxy::send($task['link']);

        // Waiting for response 200 from proxies
        if ($request['http_code'] === 200) {
            if (!$request['response'] || $request['http_code'] === 403 || $request['http_code'] === 0) {
                Redis::init()->rpush('tasks', json_encode([
                    'link' => $task['link'],
                    'class' => $task['class'],
                ]));
                
                return false;
            }
            
            $content = new Document($request['response']);
            
            // Creating class instance for scraping
            $class = new $task['class']($content, $task);
            // Run the class
            $class->scrap();

            var_dump($task['link']);
            
            return true;
        } elseif ($request['http_code'] === 404 && $task['class'] == '\\App\\Classes\\CheckLinks') { // Initializing the class for checking ads when 404
            $class = new $task['class']($content = null, $task, true);
            $class->scrap();

            return true;
        } elseif ($request['http_code'] === 404) { // Removing non-working urls without the class for checking
            return false;
        }
        
        // If response is != 200, pushing the task again
        Redis::init()->rpush('tasks', json_encode([
            'link' => $task['link'],
            'class' => $task['class'],
        ]));

        return false;
    }

    /**
     * Checking pids
     *
     * @return void
     */
    protected function checkPid()
    {
        foreach (array_keys($this->pid) as $pid) {
            $res = pcntl_waitpid($pid, $status, WNOHANG);

            if (-1 === $res || $res > 0) {
                unset($this->pid[$pid]);
            }
        }
    }

    /**
     * Cleaning the text
     *
     * @param  string $text
     * @return string
     */
    public static function clearText($text)
    {
        $text = preg_replace('/(?:&nbsp;|\h)+/u', ' ', $text);
        $text = preg_replace('/\h*(\R)\s*/u', '$1', $text);
        $text = trim($text);

        return $text;
    }
}
