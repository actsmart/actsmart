<?php
namespace actsmart\actsmart\Interpreters\NLP;

use actsmart\actsmart\Interpreters\Intent\BaseIntentInterpreter;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use GuzzleHttp\Client;

class LUISInterpreter extends BaseIntentInterpreter
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
        $this->staging = false;
        $this->subscription_key = $subscription_key;
        $this->timezone_offset = $timezone_offset;
        $this->verbose = $verbose;
        $this->spellcheck = $spellcheck;
    }

    /**
     * @param Map $utterance
     * @return Intent
     */
    public function interpretUtterance(Map $utterance) : Intent
    {
        $response = json_decode($this->queryLUISapp($utterance->get(Literals::TEXT))->getBody()->getContents());
        return new Intent(
            $response->topScoringIntent->intent,
            $utterance,
            $response->topScoringIntent->score
        );
    }

    /**
     * @param $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function queryLUISapp($message)
    {
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

    /**
     * @return string
     */
    public function getKey()
    {
        return 'interpreter.luis';
    }
}
