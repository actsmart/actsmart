<?php

namespace actsmart\actsmart\Utils\LUIS;

use GuzzleHttp\Client;

class LUISClient
{
    private $client;

    private $appUrl;

    private $appId;

    private $subscriptionKey;

    private $staging = false;

    private $timezoneOffset;

    private $verbose = true;

    private $spellcheck = true;

    public function __construct($appUrl, $appId, $subscriptionKey, $staging = false, $timezoneOffset = 0, $verbose = true, $spellcheck = true)
    {
        $this->client = new Client();
        $this->appUrl = $appUrl;
        $this->appId = $appId;
        $this->staging = $staging;
        $this->subscriptionKey = $subscriptionKey;
        $this->timezoneOffset = $timezoneOffset;
        $this->verbose = $verbose;
        $this->spellcheck = $spellcheck;
    }

    /**
     * @param $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function queryLUIS($message)
    {
        try {

            $query = $this->client->request('GET',
                $this->appUrl . '/' . $this->appId,
                [
                    'query' =>
                        [
                            'staging' => $this->staging,
                            'timezone-offset' => $this->timezoneOffset,
                            'verbose' => $this->verbose,
                            'spellcheck' => $this->spellcheck,
                            'q' => $message,
                        ],
                    'headers' =>
                        [
                            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
                        ]
                ]
            );
        } catch (\Exception $e) {
            throw new LUISRequestFailedException($e->getMessage());
        }

        if ($query->getStatusCode() == '200') {
            return new LUISResponse(json_decode($query->getBody()->getContents()));
        } else {
            return json_decode($query->getBody()->getContents());
        }
    }

}