<?php

namespace actsmart\actsmart\Conditions\Alexa;

use actsmart\actsmart\Conversations\AbstractCondition;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class DialogStateCompleted extends AbstractCondition
{
    const KEY = 'conditions.alexa.dialog_state_completed';

    const COMPLETED = 'COMPLETED';

    public function check(Map $utterance)
    {
        return $utterance->get(Literals::DIALOG_STATE) === self::COMPLETED;
    }

    public function getKey()
    {
        return self::KEY;
    }
}