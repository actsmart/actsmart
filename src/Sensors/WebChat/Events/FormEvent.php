<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class FormEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.form';

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->userId = $subject->user_id;
        $this->timestamp = time();
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    /**
     * @return Map
     */
    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_FORM);
        $utterance->put(Literals::TEXT, '');
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());

        return $utterance;
    }
}
