<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class ResponseLongTextEvent extends ResponseMessageEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.response_longtext';

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->user_id = $arguments[Literals::USER_ID];
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
        $utterance = parent::getUtterance();

        return $utterance;
    }
}
