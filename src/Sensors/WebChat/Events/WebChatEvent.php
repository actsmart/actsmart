<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

abstract class WebChatEvent extends SensorEvent implements UtteranceEvent
{
    protected $user = null;

    protected $messageId = null;

    protected $userId = null;

    protected $timestamp = null;

    public function __construct($subject, $arguments, $event_key = 'event.webchat.generic')
    {
        parent::__construct($subject, $arguments);

        $this->messageId = isset($subject->id) ? $subject->id : NULL;
        $this->userId = isset($subject->user_id) ? $subject->user_id : NULL;
        $this->author = isset($subject->author) ? $subject->author : NULL;
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
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAuthor()
    {
        return $this->author;
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

    /**
     * @return int
     */
    public function isLoggedIn()
    {
        if ($this->user && !empty($this->user->email) && $this->user->email == $this->userId) {
            return true;
        }

        return false;
    }

    public function getUtterance(): Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::USER, $this->getUser());
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::LOGGED_IN_USER, $this->isLoggedIn());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        return $utterance;
    }
}
