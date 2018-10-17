<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class ResponseMessageEvent extends WebChatEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.response_message';

    protected $text = null;

    protected $data = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->userId = $arguments[Literals::USER_ID];
        $this->timestamp = time();
        $this->text = $subject->getText();
        $this->data = null;
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
        $utterance->put(Literals::UID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
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
