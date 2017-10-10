<?php

namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\InterpreterInterface;
use actmsart\actsmart\Sensors\Slack\SlackEvent;

class SlackMessageInterpreter implements InterpreterInterface
{
    public function interpret($e)
    {
        // Check what team the message is coming from

        // Check whether it is a bot user or a normal user

        // Check whether it is a message we just sent

        if ($e->getArgument('event')->text == 'tasks') {
            return $e->getArgument('event')->text;
        } else {
            return false;
        }
    }

    public function notify()
    {
    }
}