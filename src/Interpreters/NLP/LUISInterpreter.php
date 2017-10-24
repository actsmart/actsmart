<?php
namespace actsmart\actsmart\Interpreters\NLP;

use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use GuzzleHttp\Client;

class LUISInterpreter implements InterpreterInterface
{
    const LUIS_INTERPRETER = 'luis.interpreter';

    private $client;

    private $app_url;

    private $app_id;

    private $subscription_key;

    private $staging = FALSE;

    private $timezone_offset;

    private $verbose = TRUE;

    private $spellcheck = TRUE;

    public function __construct($app_url, $app_id, $subscription_key, $staging = false, $timezone_offset = 0, $verbose = TRUE, $spellcheck = TRUE)
    {
        $this->client = new Client();
        $this->app_url = $app_url;
        $this->app_id = $app_id;
        $this->staging = FALSE;
        $this->subscription_key = $subscription_key;
        $this->timezone_offset = $timezone_offset;
        $this->verbose = $verbose;
        $this->spellcheck = $spellcheck;
    }

    public function interpret($e)
    {
        // Extract message
        $message = $e->getUtterance();
        $response = json_decode($this->queryLUISapp($message)->getBody()->getContents());
        $intent = new Intent(
            $response->topScoringIntent->intent,
            $e,
            $response->topScoringIntent->score
        );

        return $intent;
    }

    public function notify()
    {
        // TODO: Implement notify() method.
    }

    public function getKey()
    {
        return SELF::LUIS_INTERPRETER;
    }

    private function queryLUISapp($message) {
        return $this->client->request('GET',
            $this->app_url . '/' . $this->app_id,
            [
                'query' =>
                    [
                        'subscription-key' => $this->subscription_key,
                        'staging' => $this->staging ? 'false' : 'true',
                        'timezone-offset' => $this->timezone_offset,
                        'verbose' => $this->verbose ? 'false' : 'true',
                        'spellcheck' => $this->spellcheck ? 'false' : 'true',
                        'q' => $message,
                    ]
            ]
        );
    }
}