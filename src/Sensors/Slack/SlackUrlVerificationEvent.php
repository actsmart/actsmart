<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackUrlVerificationEvent extends SlackEvent
{
    const EVENT_NAME = 'slack.url_verification';
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
        parent::__construct($type, $message);

    }

    public function getName()
    {
        return SELF::EVENT_NAME;
    }


}