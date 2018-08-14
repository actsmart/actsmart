<?php

namespace actsmart\actsmart\Actuators\Facebook;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Class FacebookActuator
 *
 * Interacts with Facebook to post messages.
 */
class FacebookActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const KEY = 'actuator.facebook';
    const POSTMESSAGE = 'action.facebook.postmessage';

    private $headers = [];

    private $client;

    private $base_uri;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * The FacebookActuator determines the type of Facebook message so as to call the appropriate Facebook API endpoint.
     *
     * @param $action
     * @param array $arguments
     * @return mixed
     */
    public function perform(string $action, $arguments = [])
    {
        if ($action != self::POSTMESSAGE || !isset($arguments['message'])) {
            return null;
        }

        $this->headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $this->base_uri = $this->getBaseUri();

        $response = null;

        if (is_array($arguments['message'])) {
            $this->sendMarkSeenMessage($arguments['message'][0]);
            $this->sendTypingOnMessage($arguments['message'][0]);
            sleep(1);
            $this->sendTypingOffMessage($arguments['message'][0]);
            $response = $this->postMultipleMessages($arguments['message']);
        } else {
            $this->sendMarkSeenMessage($arguments['message']);
            $this->sendTypingOnMessage($arguments['message']);
            sleep(1);
            $this->sendTypingOffMessage($arguments['message']);
            $response = $this->postMessage($arguments['message']);
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
     * @param FacebookMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function postMessage(FacebookMessage $message)
    {
        $this->logger->debug('Attempting a facebook message.');

        return $this->client->request('POST',
            $this->base_uri, [
                'headers' => $this->headers,
                'json' => $message->getMessageToPost()
            ]);
    }

    /**
     * @param FacebookMessage[] $messages
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    private function postMultipleMessages(array $messages)
    {
        foreach ($messages as $message) {
            $response = $this->postMessage($message);
        }

        return $response;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::KEY;
    }

    /**
     * @return array
     */
    public function performsActions()
    {
        return [self::POSTMESSAGE];
    }

    /**
     * Gets the access token from the config store and appends to the uri
     *
     * @return mixed
     */
    protected function getBaseUri()
    {
        $baseUri = $this->getAgent()->getStore('store.config')->get('facebook', 'uri.base');
        $accessToken = $this->getAgent()->getStore('store.config')->get('facebook', 'access.token');

        return sprintf("%s?access_token=%s", $baseUri, $accessToken);
    }

    /**
     * Sends a message to set the sender action of typing_on to the user to show that a message is being constructed
     *
     * @param FacebookMessage $message
     */
    protected function sendMarkSeenMessage(FacebookMessage $message): void
    {
        $this->client->request('POST',
            $this->base_uri, [
                'headers' => $this->headers,
                'json' => $message->getMarkSeenMessage()
            ]);
    }

    /**
     * Sends a message to set the sender action of typing_on to the user to show that a message is being constructed
     *
     * @param FacebookMessage $message
     */
    protected function sendTypingOnMessage(FacebookMessage $message): void
    {
        $this->client->request('POST',
            $this->base_uri, [
                'headers' => $this->headers,
                'json' => $message->getTypingOnMessage()
            ]);
    }

    /**
     * Sends a message to set the sender action of typing_off to the user to show that the message has finished being
     * constructed
     *
     * @param FacebookMessage $message
     */
    protected function sendTypingOffMessage(FacebookMessage $message): void
    {
        $this->client->request('POST',
            $this->base_uri, [
                'headers' => $this->headers,
                'json' => $message->getTypingOffMessage()
            ]);
    }
}
