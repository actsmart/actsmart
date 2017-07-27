<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackEvent extends SensorEvent
{
    const EVENT_NAME = 'slack.generic';
    /**
     * The original slack message - json encoded object
     * @var object
     */
    private $message;

    /**
     * @var string
     */
    private $type;

    public function __construct($type, $message)
    {
        $this->type = $type;

        $this->message = $message;
    }

    public function getName()
    {
        return SELF::EVENT_NAME;
    }


}