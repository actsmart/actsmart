<?php

namespace actsmart\actsmart\Conditions\Alexa;

use actsmart\actsmart\Conversations\AbstractCondition;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

/**
 * Checks the value of the Dialog State. The dialog should only be deferred to Alexa if the dialog state is in progress
 */
class DialogStateInProgress extends AbstractCondition
{
    const KEY = 'conditions.alexa.dialog_state_started';

    const STARTED     = 'STARTED';
    const IN_PROGRESS = 'IN_PROGRESS';

    public function check(Map $utterance)
    {
        return
            $utterance->get(Literals::DIALOG_STATE) === self::STARTED ||
            $utterance->get(Literals::DIALOG_STATE) === self::IN_PROGRESS;
    }

    public function getKey()
    {
        return self::KEY;
    }
}