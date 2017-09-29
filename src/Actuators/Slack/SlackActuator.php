<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Actuators\Slack\SlackMessage;
use GuzzleHttp\Client;


class SlackActuator implements ActuatorInterface
{

    const SLACK_ACTUATOR_KEY = 'slack.actuator';

    public function act(SlackMessage $message)
    {
        var_dump($message->getText());
        $client = new Client([
            'base_uri' => 'https://slack.com/api/',
            'form_params' => [
                'token' => $message->getToken(),
                'channel' => $message->getChannel(),
                'text' => $message->getText(),
                /*'attachments' => json_encode([
                    [
                        "text" => $message,
                        "pretext" => "huh"
                    ]
                ]),*/
            ],
        ]);

        $ret = $client->post('chat.postMessage');
        // @todo - handle failures and throw appropriate exceptions.
        var_dump($ret->getBody()->getContents());
    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }

}