<?php

namespace actsmart\actsmart\Utils\MSTextAnalytics;

use actsmart\actsmart\Utils\MSTextAnalytics\MSTextAnalyticsRequestFailedException;
use GuzzleHttp\Client;

class TAClient
{
    private $client;

    private $appUrl;

    private $subscriptionKey;

    const LANGUAGE_ANALYSIS = 'languages';
    const DOCUMENTS = 'documents';

    public function __construct($appUrl, $subscriptionKey)
    {
        $this->client = new Client();
        $this->appUrl = $appUrl;
        $this->subscriptionKey = $subscriptionKey;
    }

    /**
     * @param $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function queryTextAnalytics($message, $analysisType)
    {
        try {

            $query = $this->client->request('POST',
                $this->appUrl . '/' . $analysisType,
                [
                    'headers' =>
                        [
                            'Content-Type' => 'application/json',
                            'Ocp-Apim-Subscription-Key' => $this->subscriptionKey
                        ],
                    'json' => $this->formatTextAnalyticsQuery($message),
                ]
            );
        } catch (\Exception $e) {
            throw new MSTextAnalyticsRequestFailedException($e->getMessage());
        }

        if ($query->getStatusCode() == '200') {
            return new TAResponse(json_decode($query->getBody()->getContents()));
        } else {
            return json_decode($query->getBody()->getContents());
        }
    }

    /**
     * @param $message
     * @return array
     */
    private function formatTextAnalyticsQuery($message)
    {
        // This is hardcoded to handle a single message for now but MSTextAnalytics can handle multiples ones.
        $object = new \stdClass();
        $object->id = 1;
        $object->text = $message;
        $documents = [
            self::DOCUMENTS => [$object]
        ];

        return $documents;
    }

}