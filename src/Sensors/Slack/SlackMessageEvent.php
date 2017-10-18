<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;

class SlackMessageEvent extends SlackEvent implements UtteranceEvent
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

    public function getUtterance() {
        return $this->getArgument('event')->text;
    }
}