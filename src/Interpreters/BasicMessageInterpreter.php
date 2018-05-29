<?php

namespace actsmart\actsmart\Interpreters;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use Symfony\Component\EventDispatcher\GenericEvent;

class BasicMessageInterpreter extends BaseInterpreter
{
    use RegularExpressionHelper;

    private $help = ['help', 'assistance', 'how does it work', 'what do i do'];

    private $hello = ['hello', 'howdy', 'hi', 'how are you'];

    public function interpret(GenericEvent $e)
    {
        if ($e instanceof UtteranceEvent) {
            $message = $e->getUtterance();

            $message = $this->removeAllUsernames($message);

            if ($this->wordsMentioned($message, [$this->help])) {
                return new Intent('ProvideHelp', $e, 1);
            }

            if ($this->wordsMentioned($message, [$this->hello])) {
                return new Intent('Hello', $e, 1);
            }
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
