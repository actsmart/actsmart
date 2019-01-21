<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class UrlClickEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.url_click';

    private $url = null;

    public function __construct($subject, $arguments = [], $key = null)
    {
        $this->event_key = $key == null ? self::EVENT_NAME : $key;
        parent::__construct($subject, $arguments, $this->event_key);

        $this->url = $subject->data->url ?? null;
    }

    public function getKey()
    {
        return $this->event_key;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_URL_CLICK);
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::TEXT, '');
        $utterance->put(Literals::URL, $this->url);

        return $utterance;
    }
}
