<?php

namespace App\Classes;

class PaginateScrap
{
    // Page content
    protected $content;
    // Task information
    protected $task;

    /**
     * A builder with the parameters
     *
     * @param object $content
     * @param array $task
     */
    public function __construct($content, $task)
    {
        $this->content = $content;
        $this->task = $task;
    }

    /**
     * Generating links
     *
     * @return void
     */
    public function scrap()
    {
        $countPage = (int)Queue::clearText(explode('/', $this->content->find('section.styles__AreaNavigation-fyba3j-1 span strong')[0])[1]);
        for ($i = 1; $i <= $countPage; $i++) {
            Redis::init()->rpush('tasks', json_encode([
                'link' => $this->task['link'] . '?page=' . $i,
                'class' => 'App\\Classes\\PaginateLinkScrap'
            ]));
        }

        return true;
    }
}
