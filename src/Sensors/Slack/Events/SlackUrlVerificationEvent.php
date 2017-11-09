<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackUrlVerificationEvent extends SlackEvent
{
    const EVENT_KEY = 'event.slack.url_verification';

    public function __construct($subject, $arguments)
    {
        parent::__construct($subject, $arguments);
    }

    public function getKey()
    {
        return SELF::EVENT_KEY;
    }

}