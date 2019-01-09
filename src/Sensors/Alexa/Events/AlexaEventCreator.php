<?php

namespace actsmart\actsmart\Sensors\Alexa\Events;

use UnexpectedValueException;

class AlexaEventCreator
{
    const MESSAGE = 'MESSAGE';

    public $eventMap = [
        self::MESSAGE => AlexaMessageEvent::class,
    ];

    /**
     * @param $event_type
     * @param $message
     * @return AlexaMessageEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->eventMap[$event_type]($message, [$event_type]);
        } else {
            throw new UnexpectedValueException("Unsupported Alexa event type " . $event_type);
        }
    }

    /**
     * @param $event_type
     * @return bool
     */
    public function supportsEvent($event_type)
    {
        if (key_exists($event_type, $this->eventMap)) {
            return true;
        } else {
            return false;
        }
    }
}
