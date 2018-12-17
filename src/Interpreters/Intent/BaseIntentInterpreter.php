<?php

namespace actsmart\actsmart\Interpreters\Intent;

use actsmart\actsmart\Interpreters\BaseInterpreter;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

abstract class BaseIntentInterpreter extends BaseInterpreter implements IntentInterpreter
{
    /**
     * Helper method to get the callback id, checking whether it has been set first
     *
     * @param Map $utterance
     * @return string
     */
    protected function getCallbackId(Map $utterance)
    {
        return $utterance->hasKey(Literals::CALLBACK_ID) ? $utterance->get(Literals::CALLBACK_ID) : null;
    }
}
