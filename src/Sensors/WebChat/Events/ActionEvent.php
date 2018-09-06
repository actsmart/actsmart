<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ActionEvent extends WebChatEvent implements UtteranceEvent
{
    const EVENT_NAME = 'event.webchat.action';

    private $callback_id = null;

    private $user_id = null;

    private $timestamp = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = []);

        $this->callback_id = $subject->callback_id ?? null;
        $this->user_id = $subject->author;
        $this->timestamp = now();
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_ACTION);
        $utterance->put(Literals::CALLBACK_ID, $this->getCallbackId());
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::UID, $this->user_id);
        $utterance->put(Literals::TIMESTAMP, $this->timestamp);
        return $utterance;
    }

    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return \Illuminate\Support\Carbon|null
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }


}
