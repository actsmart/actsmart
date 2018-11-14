<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ResponseActionEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.response_action';

    private $text;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);

        $this->userId = $arguments[Literals::USER_ID];
        $this->text = $subject->getText();
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_ACTION);
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::TEXT, $this->text);

        return $utterance;
    }
}
