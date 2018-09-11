<?php

namespace actsmart\actsmart\Interpreters;

use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Ds\Map;

class BasicMessageInterpreter extends BaseIntentInterpreter
{
    use RegularExpressionHelper;

    private $help = ['help', 'assistance', 'how does it work', 'what do i do'];

    private $hello = ['hello', 'howdy', 'hi', 'how are you'];

    /**
     * @param Map $utterance
     * @return Intent
     */
    public function interpretUtterance(Map $utterance): Intent
    {
        $message = $this->cleanseMessage($utterance->get(Literals::TEXT));

        if ($this->wordsMentioned($message, [$this->help])) {
            return new Intent('ProvideHelp', $utterance, 1);
        }

        if ($this->wordsMentioned($message, [$this->hello])) {
            return new Intent('Hello', $utterance, 1);
        }

        // Return an empty Intent if nothing matches.
        return new Intent();
    }

    public function notify()
    {
    }

    public function getKey()
    {
        return ('interpreter.basic');
    }
}
