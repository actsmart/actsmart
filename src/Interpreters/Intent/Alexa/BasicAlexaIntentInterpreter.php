<?php

namespace actsmart\actsmart\Interpreters\Intent\Alexa;

use actsmart\actsmart\Interpreters\Intent\BaseIntentInterpreter;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Utils\Literals;

class BasicAlexaIntentInterpreter extends BaseIntentInterpreter
{
    const KEY = 'interpreter.alexa.basic';

    public function interpretUtterance(\Ds\Map $utterance): \actsmart\actsmart\Interpreters\Intent\Intent
    {
        return new Intent($utterance->get(Literals::INTENT), $utterance, 1);
    }

    public function getKey()
    {
        return self::KEY;
    }
}
