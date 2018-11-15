<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ResponseListEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.response_list';

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_LIST);
        $utterance->put(Literals::SOURCE_EVENT, $this);

        return $utterance;
    }
}
