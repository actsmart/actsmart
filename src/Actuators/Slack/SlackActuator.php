<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Actuators\Slack\SlackMessage;
use Illuminate\Support\Facades\Log;
use GuzzleHttp\Client;


class SlackActuator implements ActuatorInterface
{

    const SLACK_ACTUATOR_KEY = 'slack.actuator';
    const SLACK_BASE_URI = 'https://slack.com/api/';

    public function postMessage(SlackMessage $message)
    {
        $client = new Client([
            'base_uri' => SELF::SLACK_BASE_URI,
            'form_params' => $message->getMessageToPost(),
        ]);

        $ret = $client->post('chat.postMessage');

        // @todo - handle failures and throw appropriate exceptions.
        var_dump($ret->getBody()->getContents());
    }


    public function postDialog(SlackDialog $dialog)
    {
        $client = new Client([
            'base_uri' => SELF::SLACK_BASE_URI,
            'form_params' => $dialog->getDialogToPost(),
        ]);

        $ret = $client->post('dialog.open');
        // @todo - handle failures and throw appropriate exceptions.
        var_dump($ret->getBody()->getContents());
    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }

}