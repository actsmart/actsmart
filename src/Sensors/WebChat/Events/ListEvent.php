<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ListEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.list';

    private $callbackId = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->callbackId = $subject->callback_id;
        $this->user = $subject->user ?? null;
        $this->userId = $subject->user_id;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return Map
     */
    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_LIST);
        $utterance->put(Literals::TEXT, '');
        $utterance->put(Literals::CALLBACK_ID, $this->callbackId);
        $utterance->put(Literals::SOURCE_EVENT, $this);

        return $utterance;
    }
}
