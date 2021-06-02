<?php

namespace App\Classes;

class StormProxy
{
    public static function send($url)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'cache-control: max-age=0',
            'upgrade-insecure-requests: 1',
            'DNT: 1',
            'user-agent: Mozilla/5.0 (X11; Linux x86_64; rv:89.0) Gecko/20100101 Firefox/89.0   ',
            'accept: image/webp,*/*',
            'connection: keep-alive',
            'accept-language: en-US,en;q=0.5',
            'referer: https://hotpads.com/'
        ]);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_TIMEOUT, 0);
        curl_setopt($ch, CURLOPT_PROXY, env('PROXY', ''));

        $response = curl_exec($ch);

        // get info about request
        $info = curl_getinfo($ch);
        curl_close($ch);

        return [
            'http_code' => $info['http_code'],
            'response'  => $response,
            'debug'     => $info,
        ];
    }
}
