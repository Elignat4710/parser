<?php

namespace App\Classes;

/**
 * Class for collecting the ad links from pagination
 */
class PaginateLinkScrap
{
    // Constant with a link to the site
    const SITE_URL = 'https://hotpads.com';
    
    // Page contnet
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
     * Building the links for scraper classes and passing to the queue
     *
     * @return void
     */
    public function scrap()
    {
        // Сollecting all the links on the page
        $allLinksOnPage = $this->content->find('div.AreaListingsContainer')[0]->find('li.ListingWrapper');

        
        foreach ($allLinksOnPage as $link) {
            $tempLink = $link->find('a')[0]->getAttribute('href');

            if (end(explode('/', $tempLink)) == 'building') {
                $class = 'App\\Classes\\BuildingScrap';
            } else {
                $class = 'App\\Classes\\PageUnitScrap';
            }

            Redis::init()->rpush('tasks', json_encode([
                'link' => self::SITE_URL . $tempLink,
                'class' => $class
            ]));
        }

        return true;
    }
}
