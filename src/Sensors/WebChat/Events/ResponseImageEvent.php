<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ResponseImageEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.response_image';

    private $imgSrc;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);

        $this->userId = $arguments[Literals::USER_ID];
        $this->timestamp = time();
        $this->imgSrc = $subject->getImgSrc();
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_IMAGE);
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::TEXT, $this->imgSrc);

        return $utterance;
    }
}