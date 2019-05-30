<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class TriggerEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.trigger';

    private $callbackId = null;

    private $value = null;

    protected $event_key;

    public function __construct($subject, $arguments = [], $key = null)
    {
        $this->event_key = $key == null ? self::EVENT_NAME : $key;
        parent::__construct($subject, $arguments, $this->event_key);

        $this->callbackId = $subject->data->callback_id ?? null;
        $this->value = $subject->data->value ?? null;
    }

    public function getKey()
    {
        return $this->event_key;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_TRIGGER);
        $utterance->put(Literals::CALLBACK_ID, $this->callbackId);
        $utterance->put(Literals::VALUE, $this->value);
        $utterance->put(Literals::TEXT, '');

        return $utterance;
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
