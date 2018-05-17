<?php

namespace actsmart\actsmart\Sensors\Facebook;

use actsmart\actsmart\Sensors\Facebook\Events\FacebookEventCreator;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;

/**
 * Class FacebookSensor
 * @package actsmart\actsmart\Sensors
 */
class FacebookSensor implements SensorInterface, NotifierInterface, ComponentInterface, LoggerAwareInterface
{
    use NotifierTrait, ComponentTrait, LoggerAwareTrait;

    const SENSOR_NAME = 'sensor.facebook';

    /**
     * The class that creates events based on the input.
     *
     * @var FacebookEventCreator
     */
    private $event_creator;

    public function __construct(FacebookEventCreator $event_creator)
    {
        $this->event_creator = $event_creator;
    }

    /**
     * @param SymfonyRequest $message
     */
    public function receive(SymfonyRequest $message)
    {
        $this->logger->debug('Got a message: ' . $message->getContent());
        switch ($message->getContentType()) {
            case 'json' && $message->getMethod() == 'POST':
                $facebook_message = json_decode($message->getContent());
                break;
            case $message->getMethod() == 'GET':
                $facebook_message = (object)$message->request->all();
                break;
            default:
                $this->logger->debug('Could not get message content.');
                return false;
        }

        if ($this->validateFacebookMessage($facebook_message, $message)) {
            if ($event = $this->process($facebook_message)) {
                dd($event);
                // Notify subscribers of the event
                $this->notify($event->getkey(), $event);
            }
        }
    }

    /**
     * Process the slack message and creates an appropriate Slack event based on the message type.
     * @param $slack_message
     * @return Events\SlackEvent|null
     */
    public function process($facebook_message)
    {
        try {
            if (isset($facebook_message->hub_mode) && $facebook_message->hub_mode == 'subscribe') {
                return $this->event_creator->createEvent('url_verification', $facebook_message);
            }
            if (isset($facebook_message->entry[0]->messaging)) {
                dump('hi');
                return $this->event_creator->createEvent('messages', $facebook_message->entry[0]);
            }

        } catch (FacebookEventTypeNotSupportedException $e) {
            $this->logger->notice('Unsupported Facebook message');
            return null;
        }
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::SENSOR_NAME;
    }


    /**
     * @param $slack_message
     * @return bool
     * @throws \Exception
     */
    private function validateFacebookMessage($facebook_message, $original_request)
    {
        if (isset($facebook_message->hub_verify_toke)) {
            if ($facebook_message->hub_verify_token != $this->agent->getStore('store.config')->get('facebook', 'app.token')) {
                throw new FacebookMessageInvalidException("Could not validate Facebook Message");
            } else {
                return true;
            }
        } else {
            //@todo SHA1 verification on original request - https://developers.facebook.com/docs/messenger-platform/webhook#security
            return true;
        }

    }
}
