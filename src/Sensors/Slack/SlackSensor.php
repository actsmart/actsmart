<?php

namespace actsmart\actsmart\Sensors\Slack;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use Symfony\Component\EventDispatcher\EventDispatcher;
use actsmart\actsmart\Sensors\Slack\SlackEvent;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\SensorEvent;

/**
 * Class SlackSensor
 * @package actsmart\actsmart\Sensors
 */
class SlackSensor implements SensorInterface
{
    const SENSOR_NAME = 'SlackSensor';

    const SENSOR_EVENT_NAME = 'slack.event';

    /**
     * The class that creates events based on the input.
     *
     * @var SlackEventCreator
     */
    private $event_creator;

    private $token;

    /**
     * Use to notify
     * @var EventDispatcher;
     */
    private $event_dispatcher;

    public function __construct(SlackEventCreator $event_creator, EventDispatcher $dispatcher, $slack_verification_token)
    {
        $this->event_creator = $event_creator;
        $this->event_dispatcher = $dispatcher;
        $this->token = $slack_verification_token;
    }

    /**
     * @param SymfonyRequest $message
     */
    public function receive(SymfonyRequest $message)
    {
        $slack_message = json_decode($message->getContent());

        try {
            if ($this->validateSlackMessage($slack_message)) {
                $this->notify($this->process($slack_message));
            }
        } catch (\Exception $e) {
            // @todo - log issue
        }


    }


    /**
     * @param $slack_message
     * @return actsmart\actsmart\Sensors\Slack\SlackEvent
     */
    public function process($slack_message)
    {
        if ($slack_message->type == 'url_verification)') {
            return $this->event_creator->createEvent($slack_message->type, $slack_message);
        }

        if ($slack_message->type == 'event_callback') {
            return $this->event_creator->createEvent($slack_message->event->type, $slack_message);
        }


    }

    /**
     * @param SensorEvent $e
     */
    public function notify(SensorEvent $e)
    {
        $this->event_dispatcher->dispatch(self::SENSOR_EVENT_NAME, $e);
    }

    public function getName()
    {
        return self::SENSOR_NAME;
    }

    public function getEventName()
    {
        return self::SENSOR_EVENT_NAME;
    }

    /**
     * @param $slack_message
     * @return bool
     * @throws \Exception
     */
    private function validateSlackMessage($slack_message)
    {
        if ($slack_message->token != $this->token) {
            throw new \Exception("Could not validate Slack Message");
        } else {
            return true;
        }
    }
}