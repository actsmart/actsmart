<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackEvent extends SensorEvent
{
    public function __construct($subject, $arguments, $event_key = 'event.slack.generic')
    {
        parent::__construct($subject, $arguments);
        $this->event_key = $event_key;
    }
}
