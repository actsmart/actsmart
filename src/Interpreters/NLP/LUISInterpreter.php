<?php
namespace actsmart\actsmart\Interpreters\NLP;

use actsmart\actsmart\Interpreters\BaseInterpreter;
use actsmart\actsmart\Interpreters\Intent;
use Symfony\Component\EventDispatcher\GenericEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use GuzzleHttp\Client;

class LUISInterpreter extends BaseInterpreter
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
     * @param GenericEvent $e
     * @return Intent
     */
    public function interpret(GenericEvent $e)
    {
        if ($e instanceof UtteranceEvent) {
            $message = $e->getUtterance();
            $response = json_decode($this->queryLUISapp($message)->getBody()->getContents());
            return new Intent(
                $response->topScoringIntent->intent,
                $e,
                $response->topScoringIntent->score
            );
        }

        return new Intent();
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
