<?php

namespace actsmart\actsmart\Utils\Qna;

use GuzzleHttp\Client;

class QnAClient
{
    private $client;

    private $uri;

    private $kbId;

    private $authCode;

    public function __construct($uri, $KBId, $authCode)
    {
        $this->client = new Client();
        $this->uri = $uri;
        $this->kbId = $KBId;
        $this->authCode = $authCode;
    }

    public function queryQnA($question)
    {
        $query = $this->client->request('POST',
            $this->uri . '/' . $this->kbId . '/generateAnswer', [
                'headers' => [
                    'Authorization' => 'EndpointKey ' . $this->authCode,
                    'Content-Type' => 'application/json',
                ],
                'json' => ['question' => $question
                ]
            ]);

        if ($query->getStatusCode() == '200') {
            return new QnAResponse(json_decode($query->getBody()->getContents()));
        } else {
            return false;
        }
    }
}