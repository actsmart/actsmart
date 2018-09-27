<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use Ds\Map;

class ChatOpenEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.chat_open';

    private $userId = null;

    private $timestamp = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = [], self::EVENT_NAME);

        $this->userId = $subject->userId;
        $this->timestamp = time();
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    // This needs to go away but we have to clean up the hierarchy from SensorEvent to WebChatEvent
    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        return $utterance;
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
