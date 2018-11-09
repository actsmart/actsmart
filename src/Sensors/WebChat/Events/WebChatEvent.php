<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;

abstract class WebChatEvent extends SensorEvent implements UtteranceEvent
{
    protected $messageId = null;
    protected $userId = null;

    protected $timestamp = null;

    public function __construct($subject, $arguments, $event_key = 'event.webchat.generic')
    {
        parent::__construct($subject, $arguments);

        $this->messageId = isset($subject->id) ? $subject->id : NULL;
        $this->userId = isset($subject->user_id) ? $subject->user_id : NULL;
        $this->timestamp = time();
        $this->event_key = $event_key;
    }

    /**
     * @return string
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
