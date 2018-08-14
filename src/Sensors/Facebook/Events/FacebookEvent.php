<?php

namespace actsmart\actsmart\Sensors\Facebook\Events;

use actsmart\actsmart\Sensors\SensorEvent;

class FacebookEvent extends SensorEvent
{
    public function __construct($subject, $arguments, $event_key = 'event.facebook.generic')
    {
        parent::__construct($subject, $arguments);
        $this->event_key = $event_key;
    }
}
