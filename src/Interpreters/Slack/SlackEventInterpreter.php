<?php
namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\BaseInterpreter;

class SlackEventInterpreter extends BaseInterpreter
{



    /**
     * @return string
     */
    public function getKey()
    {
        return 'interpreter.slack.event';
    }
}