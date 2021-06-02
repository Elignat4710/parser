<?php

namespace App\Classes;

/**
 * Scraping building url
 */
class BuildingScrap
{
    // Constant with a link to the site
    const SITE_URL = 'https://hotpads.com';
    
    // Page content
    protected $content;
    // Task information
    protected $task;

    /**
     * A builder with the parameters
     *
     * @param  object $content
     * @param  array $task
     */
    public function __construct($content, $task)
    {
        $this->content = $content;
        $this->task = $task;
    }

    /**
     * Collecting all links from the page and passing to the queue
     *
     * @return void
     */
    public function scrap()
    {
        $buildingContents = $this->content->find('div.BuildingHdp-content')[0]->find('a');

        foreach ($buildingContents as $unit) {
            Redis::init()->rpush('tasks', json_encode([
                'link' => $unit->getAttribute('href'),
                'class' => 'App\\Classes\\PageUnitScrap'
            ]));
        }
    }
}
