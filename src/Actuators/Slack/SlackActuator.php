<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Actuators\SlackNotificationEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class SlackActuator
 * @package actsmart\actsmart\Actuators\Slack
 *
 * Interacts with Slack to post messages.
 */
class SlackActuator implements NotifierInterface, ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use NotifierTrait, LoggerAwareTrait, ComponentTrait;

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
     * @param array $arguments
     * @return mixed
     */
    public function perform(string $action, $arguments = [])
    {
        if ($action != 'action.slack.postmessage' || !isset($arguments['message'])) {
            return null;
        }

        $this->headers = [
            'Authorization' => 'Bearer ' . $this->getAgent()->getStore('store.config')->get('slackworkspace_'. $arguments['message']->getWorkspace(), 'bot_token'),
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $this->slack_base_uri = $this->getAgent()->getStore('store.config')->get('slack', 'uri.base');

        $message = $arguments['message'];

        $response = null;

        // Determine the type
        if ($arguments['message'] instanceof SlackEphemeralMessage) {
            $event = $this->createNotificationEvent($arguments);
            $this->notify($event->getkey(), $event);

            $response = $this->postMessage($message, self::EPHEMERAL_MESSAGE);
        }

        if ($arguments['message'] instanceof SlackStandardMessage) {
            $event = $this->createNotificationEvent($arguments);
            $this->notify($event->getkey(), $event);

            $response = $this->postMessage($message, self::STANDARD_MESSAGE);
        }

        if ($arguments['message'] instanceof SlackUpdateMessage) {
            $event = $this->createNotificationEvent($arguments);
            $this->notify($event->getkey(), $event);

            $response = $this->postUpdate($message);
        }

        if ($arguments['message'] instanceof SlackDialog) {
            $event = $this->createNotificationEvent($arguments);
            $this->notify($event->getkey(), $event);

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
     * @param $arguments
     * @return SlackNotificationEvent
     */
    protected function createNotificationEvent($arguments)
    {
        $message = $arguments['message'];
        $platform_user_id = (!empty($arguments['platform_user_id'])) ? $arguments['platform_user_id'] : null;
        $platform_channel_id = (!empty($arguments['platform_channel_id'])) ? $arguments['platform_channel_id'] : null;
        $notification_name = $arguments['notification_name'];

        $event_arguments = [
            'platform_user_id' => $platform_user_id,
            'platform_channel_id' => $platform_channel_id,
            'notification_name' => $notification_name,
        ];

        return new SlackNotificationEvent($message, $event_arguments);
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
