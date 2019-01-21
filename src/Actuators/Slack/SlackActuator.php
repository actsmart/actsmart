<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use GuzzleHttp\Client;
use Ds\Map;

/**
 * Class SlackActuator
 * @package actsmart\actsmart\Actuators\Slack
 *
 * Interacts with Slack to post messages.
 */
class SlackActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const STANDARD_MESSAGE = 'postMessage';
    const EPHEMERAL_MESSAGE = 'postEphemeral';

    private $headers = [];

    private $client;

    private $slack_base_uri;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * The SlackActuator determines the type of Slack message so as to call the apporpriate Slack API endpoint.
     *
     * @param $action
     * @param Map $arguments
     * @return mixed
     */
    public function perform(string $action, Map $arguments = null)
    {
        try {
            $message = $arguments->get(Literals::MESSAGE);
        } catch (\OutOfBoundsException $e) {
            return null;
        }

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->getAgent()->getStore(Literals::CONTEXT_STORE)->getInformation('slackworkspace_'. $message->getWorkspace(), 'bot_token')->getValue(),
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $this->slack_base_uri = $this->getAgent()->getStore(Literals::CONTEXT_STORE)->getInformation('slack', 'uri.base')->getValue();

        $response = null;

        // Determine the type
        if ($message instanceof SlackEphemeralMessage) {
            $response = $this->postMessage($message, self::EPHEMERAL_MESSAGE);
        }

        if ($message instanceof SlackStandardMessage) {
            $response = $this->postMessage($message, self::STANDARD_MESSAGE);
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
     * @param String $type
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function postMessage(SlackMessage $message, $type)
    {
        $this->logger->debug(sprintf('Attempting a message of type: %s.', $type));

        return $this->client->request('POST',
            $this->slack_base_uri . 'chat.'. $type, [
            'headers' => $this->headers,
            'json' => $message->getMessageToPost()
        ]);
    }

    /**
     * @param SlackDialog $dialog
     */
    public function postDialog(SlackDialog $dialog)
    {
        $this->logger->debug('Attempting a message of type: dialog.open');

        return $this->client->request('POST',
            $this->slack_base_uri . 'dialog.open' ,[
            'headers' => $this->headers,
            'json' => $dialog->getDialogToPost()
        ]);
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
