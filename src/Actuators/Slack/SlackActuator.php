<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use GuzzleHttp\Client;


class SlackActuator implements ActuatorInterface
{

    const SLACK_ACTUATOR_KEY = 'slack.actuator';

    public function act($message)
    {
        $client = new Client([
            'base_uri' => 'https://slack.com/api/',
            'form_params' => [
                'token' => env('SLACK_OAUTH_TOKEN_TEST'),
                'channel' => 'C5GA54NT0',
                'text' =>   'zap',
            ],
        ]);

        $ret = $client->post('chat.postMessage');
        dd(json_decode($ret->getBody()));

    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }

}