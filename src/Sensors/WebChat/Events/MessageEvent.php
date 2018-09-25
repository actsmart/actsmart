<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class MessageEvent extends WebChatEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.message';

    protected $user_id = null;

    protected $timestamp = null;

    protected $text = null;

    protected $data = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = []);

        // TODO pull the values out of the message
        $this->user_id = $subject->author;
        // TODO What are we doing here? Should this be a datetime?
        $this->timestamp = now();
        $this->text = $subject->data->text ?? null;
        $this->data = $subject->data ?? null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_MESSAGE);
        $utterance->put(Literals::TEXT, $this->getTextMessage());
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::UID, $this->user_id);
        $utterance->put(Literals::TIMESTAMP, $this->timestamp);
        return $utterance;
    }

    /**
     * @return mixed
     */
    public function getTextMessage()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }
}
