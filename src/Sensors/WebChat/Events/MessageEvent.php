<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class MessageEvent extends WebChatEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.message';

    protected $text = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->user = $subject->user ?? null;
        $this->text = $subject->data->text ?? null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_MESSAGE);
        $utterance->put(Literals::TEXT, $this->getTextMessage());
        $utterance->put(Literals::SOURCE_EVENT, $this);

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
