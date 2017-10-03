<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackMessageEvent extends SlackEvent
{
    const EVENT_NAME = 'slack.message';

    public function __construct($type, $message)
    {
        parent::__construct($type, $message);

    }

    public function getName()
    {
        return SELF::EVENT_NAME;
    }


}