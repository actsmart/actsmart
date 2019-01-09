<?php

namespace actsmart\actsmart\Sensors\Alexa;

use actsmart\actsmart\Sensors\Alexa\Events\AlexaEventCreator;
use actsmart\actsmart\Sensors\Alexa\Events\AlexaMessageEvent;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request;

class AlexaSensor implements SensorInterface, NotifierInterface, ComponentInterface, LoggerAwareInterface
{
    use NotifierTrait, ComponentTrait, LoggerAwareTrait;

    const SENSOR_NAME = 'sensor.alexa';

    /**
     * The class that creates events based on the input.
     *
     * @var AlexaEventCreator
     */
    private $eventCreator;

    public function __construct(AlexaEventCreator $eventCreator)
    {
        $this->eventCreator = $eventCreator;
    }

    /**
     * @param Request $message
     */
    public function receive($message)
    {
        $this->logger->debug('Got a message: ' . $message->getContent());
        $message = json_decode($message->getContent());
        $event = $this->process($message);

        // Notify listeners of the actual event.
        $this->notify($event->getkey(), $event);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::SENSOR_NAME;
    }

    /**
     * Process the input.
     *
     * @param $message
     * @return AlexaMessageEvent
     */
    public function process($message)
    {
        $event_type = AlexaEventCreator::MESSAGE;

        return $this->eventCreator->createEvent($event_type, $message);
    }
}
