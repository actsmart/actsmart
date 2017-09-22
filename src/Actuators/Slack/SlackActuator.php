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
            'headers'  => [
                'Authorization' => 'Bearer ' . 'xoxb-246060899542-Lu4bB78ZutZyvLeft8We5jew',
            ],
        ]);

        $ret = $client->get($path);
        dd($ret);

    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }

}