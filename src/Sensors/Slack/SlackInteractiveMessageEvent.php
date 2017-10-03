<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackInteractiveMessageEvent extends SlackEvent
{
    const EVENT_NAME = 'slack.interactive_message';

    private $callback_id;

    private $trigger_id;

    public function __construct($type, $message)
    {
        parent::__construct($type, $message);

        $this->callback_id = $message->callback_id;

        $this->trigger_id = $message->trigger_id;

    }

    public function getName()
    {
        return SELF::EVENT_NAME;
    }


    /**
     * @return mixed
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return mixed
     */
    public function getTriggerId()
    {
        return $this->trigger_id;
    }




}