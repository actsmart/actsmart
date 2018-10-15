<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ResponseFormEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.response_form';

    private $user_id = null;

    private $timestamp = null;

    private $text;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);

        $this->user_id = $arguments[Literals::USER_ID];
        $this->timestamp = time();
        $this->text = $subject->getText();
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
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::UID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::TEXT, $this->text);

        return $utterance;
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