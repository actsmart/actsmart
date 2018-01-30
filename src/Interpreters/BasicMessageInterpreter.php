<?php

namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\InterpreterInterface;

class BasicSlackMessageInterpreter implements InterpreterInterface
{
    public function interpret($e)
    {
        if ($e instanceof UtteranceEvent) {
            $message = $e->getUtterance();
        }

    }

    public function notify()
    {
    }
}
