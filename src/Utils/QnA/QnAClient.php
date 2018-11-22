<?php

namespace actsmart\actsmart\Utils\Qna;

use GuzzleHttp\Client;

class QnAClient
{
    private $client;

    private $uri;

    private $KBId;

    private $authCode;

    public function __construct($uri, $KBId, $authCode)
    {
        $this->client = new Client();
        $this->uri = $uri;
        $this->KBId = $KBId;
        $this->authCode = $authCode;
    }

    public function queryQnA($question)
    {
        try {
            $query = $this->client->request('POST',
                $this->uri . '/' . $this->KBId . '/generateAnswer', [
                    'headers' => [
                        'Authorization' => 'EndpointKey ' . $this->authCode,
                        'Content-Type' => 'application/json',
                    ],
                    'json' => ['question' => $question
                    ]
                ]);
        } catch (\Exception $e) {
            throw new QnARequestFailedException($e->getMessage());
        }

        if ($query->getStatusCode() == '200') {
            return new QnAResponse(json_decode($query->getBody()->getContents()));
        } else {
            return false;
        }
    }
}