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

    private $client;

    public function __construct()
    {
        $client = new Client([
            'base_uri' => SELF::SLACK_BASE_URI,
        ]);

        $this->client = $client;
    }

    public function postMessage(SlackMessage $message)
    {
        $ret = $this->client->post('chat.postMessage', ['form_params' => $message->getMessageToPost()]);

        // @todo - handle failures and throw appropriate exceptions.
        return json_decode($ret->getBody()->getContents());
    }


    public function postDialog(SlackDialog $dialog)
    {
        $ret = $this->client->post('dialog.open', ['form_params' => $dialog->getDialogToPost()]);
        // @todo - handle failures and throw appropriate exceptions.
        $ret->getBody()->getContents();
    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }
}