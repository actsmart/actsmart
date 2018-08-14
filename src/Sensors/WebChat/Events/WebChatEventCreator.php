<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use UnexpectedValueException;

class WebChatEventCreator
{
    public $eventMap = [
        'message' => MessageEvent::class,
    ];

    /**
     * @param $event_type
     * @param $message
     * @return WebChatEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->eventMap[$event_type]($message, [$event_type]);
        } else {
            throw new UnexpectedValueException("Unsupported Web Chat event type " . $event_type);
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
