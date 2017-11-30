<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Psr\Log\LoggerAwareInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareTrait;

/**
 * Class SlackActuator
 * @package actsmart\actsmart\Actuators\Slack
 *
 * Interacts with Slack to post messages.
 */
class SlackActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const SLACK_BASE_URI = 'https://slack.com/api/';

    private $headers = [];

    private $client;

    public function __construct()
    {
        $client = new Client();
        $this->client = $client;

    }

    /**
     * The SlackActuator determines the type of slack message so as to call the apporpriate Slack API endpoint.
     *
     * @param $action
     * @param SlackMessage $message
     * @return mixed
     */
    public function perform(string $action, $message)
    {
        if ($action != 'action.slack.postmessage') {
            return null;
        }

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->getAgent()->getStore('store.config')->get('slackworkspace_'. $message->getWorkspace(), 'bot_token'),
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $response = null;

        // Determine the type
        if ($message instanceof SlackEphemeralMessage) {
            $response = $this->postEphemeral($message);
        }

        if ($message instanceof SlackStandardMessage) {
            $response = $this->postStandard($message);
        }

        if ($message instanceof SlackUpdateMessage) {
            $response = $this->postUpdate($message);
        }

        if ($message instanceof SlackDialog) {
            $response = $this->postDialog($message);
        }


        if ($response) {
            // @todo - handle failures and throw appropriate exceptions.
            $this->logger->debug($response->getStatusCode());
            $content_body = $response->getBody()->getContents();
            $this->logger->debug($content_body);

            return json_decode($content_body);
        }
    }

    /**
     * @param SlackMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function postStandard(SlackMessage $message)
    {
        $this->logger->debug('Attempting to post a Standard message.');

        return $this->client->request('POST',
            self::SLACK_BASE_URI . 'chat.postMessage', [
                'headers' => $this->headers,
                'json' => $message->getMessageToPost()
            ]);
    }

    /**
     * @param SlackMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function postEphemeral(SlackMessage $message)
    {
        $this->logger->debug('Attempting to post an ephemeral message.');

        return $this->client->request('POST',
            self::SLACK_BASE_URI . 'chat.postEphemeral',[
            'headers' => $this->headers,
            'json' => $message->getMessageToPost()
        ]);
    }

    /**
     * @param SlackDialog $dialog
     */
    public function postDialog(SlackDialog $dialog)
    {
        $ret = $this->client->post('dialog.open', ['form_params' => $dialog->getDialogToPost()]);
        // @todo - handle failures and throw appropriate exceptions.
        $ret->getBody()->getContents();
    }

    /**
     * @param SlackMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function postUpdate(SlackMessage $message)
    {
        $this->logger->debug('Attempting to post an update message.');

        // Creating a separate client here since the URL is completely different
        return $this->client->request('POST', $message->getResponseUrl(), [
            'headers' => $this->headers,
            'json' => $message->getMessageToPost()
        ]);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'actuator.slack';
    }

    /**
     * @return array
     */
    public function performsActions()
    {
        return ['action.slack.postmessage'];
    }
}
