<?php

namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\InterpreterInterface;
use actmsart\actsmart\Sensors\Slack\SlackEvent;

class SlackMessageInterpreter implements InterpreterInterface
{
    public function interpret($e)
    {
        return $e->getArgument('event')->text.'zap';
    }

    public function notify()
    {

    }
}