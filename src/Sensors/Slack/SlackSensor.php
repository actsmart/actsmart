<?php

namespace actsmart\actsmart\Sensors\Slack;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\Slack\Events\SlackEventCreator;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;

/**
 * Class SlackSensor
 * @package actsmart\actsmart\Sensors
 */
class SlackSensor implements SensorInterface, NotifierInterface, ComponentInterface, LoggerAwareInterface
{
    use NotifierTrait, ComponentTrait, LoggerAwareTrait;

    const SENSOR_NAME = 'sensor.slack';

    /**
     * The class that creates events based on the input.
     *
     * @var SlackEventCreator
     */
    private $event_creator;

    public function __construct(SlackEventCreator $event_creator)
    {
        $this->event_creator = $event_creator;
    }

    /**
     * @param SymfonyRequest $message
     */
    public function receive(SymfonyRequest $message)
    {
        $this->logger->debug('Got a message: ' . $message->getContent());
        $slack_message = json_decode($message->getContent());

        if ($slack_message == null) {
            // Let us try and see if it is one of those that come as a payload.
            $slack_message = json_decode(urldecode($message->get('payload')));
        }

        if ($this->validateSlackMessage($slack_message)) {
            $event = $this->process($slack_message);
            $this->notify($event->getkey(), $event);
        }
    }

    /**
     * @param $slack_message
     * @return Events\SlackEvent|null
     */
    public function process($slack_message)
    {
        try {
            switch ($slack_message->type) {
                case 'url_verification':
                    return $this->event_creator->createEvent($slack_message->type, $slack_message);
                    break;
                case 'event_callback':
                    // If it is an event callback then we need to check whether the message has a subtype as well
                    $message_type = isset($slack_message->event->subtype) ? $slack_message->event->subtype : $slack_message->event_type;
                    return $this->event_creator->createEvent($message_type, $slack_message);
                    break;
                case 'interactive_message':
                    return $this->event_creator->createEvent($slack_message->type, $slack_message);
                    break;
            }
        } catch (SlackEventTypeNotSupportedException $e) {
            //
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
    private function validateSlackMessage($slack_message)
    {
        if ($slack_message->token != $this->agent->getStore('store.config')->get('token.slack')) {
            throw new SlackMessageInvalidException("Could not validate Slack Message");
        } else {
            return true;
        }
    }
}
