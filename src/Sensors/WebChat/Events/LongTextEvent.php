<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class LongTextEvent extends MessageEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.longtext';

    protected $callback_id;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, self::EVENT_NAME);

        $this->callback_id = $subject->data->callback_id ?? null;
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
        $utterance->put(Literals::CALLBACK_ID, $this->callback_id);

        return $utterance;
    }
}
