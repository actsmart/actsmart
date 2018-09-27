<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ActionEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.action';

    private $callback_id = null;

    private $user_id = null;

    private $timestamp = null;

    private $text;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = []);

        $this->callback_id = $subject->data->callback_id ?? null;
        $this->user_id = $subject->user_id;
        $this->timestamp = time();
        $this->text = $subject->data->text ?? null;
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
        $utterance->put(Literals::UID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::TEXT, $this->text);

        return $utterance;
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
