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
        $client = new Client();

        $this->client = $client;
    }

    public function postMessage(SlackMessage $message)
    {
        // Determine the type
        if ($message->getType() == 'Ephemeral') return $this->postEphemeral($message);

        if ($message->getType() == 'Standard') return $this->postStandard($message);

        if ($message->getType() == 'Update') return $this->postUpdate($message);

        if ($message->getType() == 'Dialog') return $this->postDialog($message);
    }


    public function postStandard(SlackMessage $message)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $message->getToken(),
            'Accept' => 'application/json',
            'charset' => 'utf-8',
        ];

        Log::debug('Attempting to post a Standard message.');

        $response = $this->client->request('POST',
            self::SLACK_BASE_URI . 'chat.postMessage', [
                'headers' => $headers,
                'json' => $message->getMessageToPost()
            ]);

        // @todo - handle failures and throw appropriate exceptions.
        Log::debug($response->getStatusCode());
        Log::debug($response->getBody()->getContents());

        dd($response->getBody()->getContents());

        return json_decode($response->getBody()->getContents());
    }

    public function postEphemeral(SlackMessage $message)
    {
        Log::debug('Attempting to post an ephemeral message.');

        $ret = $this->client->post('chat.postEphemeral', ['form_params' => $message->getMessageToPost()]);

        // @todo - handle failures and throw appropriate exceptions.
        return json_decode($ret->getBody()->getContents());
    }


    public function postDialog(SlackDialog $dialog)
    {
        $ret = $this->client->post('dialog.open', ['form_params' => $dialog->getDialogToPost()]);
        // @todo - handle failures and throw appropriate exceptions.
        $ret->getBody()->getContents();
    }

    public function postUpdate(SlackMessage $message)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $message->getToken(),
            'Accept'        => 'application/json',
        ];

        Log::debug('Attempting to post an update message.');

        // Creating a separate client here since the URL is completely different
        $response = $this->client->request('POST', $message->getResponseUrl(), [
            'headers' => $headers,
            'json' => $message->getMessageToPost()
        ]);
        // @todo - handle failures and throw appropriate exceptions.
        $response->getBody()->getContents();
    }

    public function getKey()
    {
        return SELF::SLACK_ACTUATOR_KEY;
    }
}