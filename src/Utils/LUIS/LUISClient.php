<?php

namespace actsmart\actsmart\Utils\LUIS;

use GuzzleHttp\Client;

class LUISClient
{
    private $client;

    private $app_url;

    private $app_id;

    private $subscription_key;

    private $staging = false;

    private $timezone_offset;

    private $verbose = true;

    private $spellcheck = true;

    public function __construct($app_url, $app_id, $subscription_key, $staging = false, $timezone_offset = 0, $verbose = true, $spellcheck = true)
    {
        $this->client = new Client();
        $this->app_url = $app_url;
        $this->app_id = $app_id;
        $this->staging = $staging;
        $this->subscription_key = $subscription_key;
        $this->timezone_offset = $timezone_offset;
        $this->verbose = $verbose;
        $this->spellcheck = $spellcheck;
    }

    /**
     * @param $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function queryLUIS($message)
    {
        $query =  $this->client->request('GET',
            $this->app_url . '/' . $this->app_id,
            [
                'query' =>
                    [
                        'staging' => $this->staging,
                        'timezone-offset' => $this->timezone_offset,
                        'verbose' => $this->verbose,
                        'spellcheck' => $this->spellcheck,
                        'q' => $message,
                    ],
                'headers' =>
                    [
                        'Ocp-Apim-Subscription-Key' => $this->subscription_key
                    ]
            ]
        );

        if ($query->getStatusCode() == '200') {
            return new LUISResponse(json_decode($query->getBody()->getContents()));
        } else {
            return false;
        }

    }

}