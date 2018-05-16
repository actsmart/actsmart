<?php

namespace actsmart\actsmart\Sensors\Facebook\Events;

use actsmart\actsmart\Sensors\Facebook\FacebookEventTypeNotSupportedException;
use actsmart\actsmart\Sensors\Facebook\Events\FacebookUrlVerificationEvent;

class FacebookEventCreator
{
    /**
     * Map a Facebook event type to a FacebookEvent class
     * @var array
     */
    public $facebook_event_map = [
        'url_verification' => FacebookUrlVerificationEvent::class,
    ];

    /**
     * @param $event_type
     * @param $message
     * @return FacebookEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->facebook_event_map[$event_type]($message, [$event_type]);
        } else {
            throw new FacebookEventTypeNotSupportedException("Unsupported Facebook event type " . $event_type);
        }
    }

    /**
     * @param $event_type
     * @return bool
     */
    public function supportsEvent($event_type)
    {
        if (key_exists($event_type, $this->facebook_event_map)) {
            return true;
        } else {
            return false;
        }
    }
}
