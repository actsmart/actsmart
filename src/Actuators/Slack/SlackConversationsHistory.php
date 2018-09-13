<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Psr\Log\LoggerAwareInterface;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareTrait;
use Ds\Map;

class SlackConversationsHistory implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    private $token;

    private $client;

    private $slack_base_uri;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function perform(string $action, Map $arguments = null)
    {
        $this->token = $this->getAgent()->getStore('store.config')->get('slackworkspace_'. $arguments->get('workspace'), 'bot_token');

        $this->slack_base_uri = $this->getAgent()->getStore('store.config')->get('slack', 'uri.base');

        $response = $this->getMessage($arguments->get('channel'), $arguments->get('timestamp'));

        if ($response) {
            $this->logger->debug($response->getStatusCode());
            $content_body = $response->getBody()->getContents();
            $this->logger->debug($content_body);

            return json_decode($content_body);
        }
    }

    protected function getMessage($channel, $timestamp)
    {
        return $this->client->request('GET',
            $this->slack_base_uri . 'conversations.history?token=' . $this->token . '&channel=' . $channel . '&count=1&inclusive=1&latest=' . $timestamp);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'actuator.slackconversationshistory';
    }

    /**
     * @return array
     */
    public function performsActions()
    {
        return ['action.slack.slackconversationshistory'];
    }
}
