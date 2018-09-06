<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\SensorEvent;

abstract class WebChatEvent extends SensorEvent
{
    public function __construct($subject, $arguments, $event_key = 'event.webchat.generic')
    {
        parent::__construct($subject, $arguments);
        $this->event_key = $event_key;
    }
}
