<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Psr\Log\LoggerAwareInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareTrait;

class SlackActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const SLACK_BASE_URI = 'https://slack.com/api/';

    private $client;

    public function __construct()
    {
        $client = new Client();

        $this->client = $client;
    }

    public function perform($action, $message)
    {
        // Determine the type
        if ($message instanceof SlackEphemeralMessage) {
            return $this->postEphemeral($message);
        }

        if ($message instanceof SlackStandardMessage) {
            return $this->postStandard($message);
        }

        if ($message instanceof SlackUpdateMessage) {
            return $this->postUpdate($message);
        }

        if ($message instanceof SlackDialog) {
            return $this->postDialog($message);
        }
    }


    public function postStandard(SlackMessage $message)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->getAgent()->getStore('store.config')->get('oauth_token.slack'),
            'Accept' => 'application/json',
            'charset' => 'utf-8',
        ];

        $this->logger->debug('Attempting to post a Standard message.');

        $response = $this->client->request('POST',
            self::SLACK_BASE_URI . 'chat.postMessage', [
                'headers' => $headers,
                'json' => $message->getMessageToPost()
            ]);

        // @todo - handle failures and throw appropriate exceptions.
        $this->logger->debug($response->getStatusCode());
        $this->logger->debug($response->getBody()->getContents());


        return json_decode($response->getBody()->getContents());
    }

    public function postEphemeral(SlackMessage $message)
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->getAgent()->getStore('store.config')->get('oauth_token.slack'),
            'Accept' => 'application/json',
            'charset' => 'utf-8',
        ];

        $this->logger->debug('Attempting to post an ephemeral message.');

        $response = $this->client->request('POST',
            self::SLACK_BASE_URI . 'chat.postEphemeral',[
            'headers' => $headers,
            'json' => $message->getMessageToPost()
        ]);

        // @todo - handle failures and throw appropriate exceptions.
        $this->logger->debug($response->getStatusCode());
        $this->logger->debug($response->getBody()->getContents());


        return json_decode($response->getBody()->getContents());
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

        $this->logger->debug('Attempting to post an update message.');

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
        return 'actuator.slack';
    }

    public function performsActions()
    {
        return ['action.slack.postmessage'];
    }
}
