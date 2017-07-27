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
     * The slack token corresponding to the Slack application
     *
     * @var string
     */
    private $slack_token;

    /**
     * The class that creates events based on the input.
     *
     * @var SlackEventCreator
     */
    private $event_creator;

    /**
     * Use to notify
     * @var EventDispatcher;
     */
    private $event_dispatcher;

    public function __construct($slack_token, SlackEventCreator $event_creator, EventDispatcher $dispatcher)
    {
        $this->slack_token = $slack_token;
        $this->event_creator = $event_creator;
        $this->event_dispatcher = $dispatcher;
    }

    /**
     * @param SymfonyRequest $message
     */
    public function receive(SymfonyRequest $message)
    {
        $slack_message = json_decode($message->getContent());
        $this->notify($this->process($slack_message));
    }


    /**
     * @param $slack_message
     * @return actsmart\actsmart\Sensors\Slack\SlackEvent
     */
    public function process($slack_message)
    {
        return $this->event_creator->createEvent($slack_message->type, $slack_message);
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
}