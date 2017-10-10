<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Controllers\Active\ActiveController;
use actsmart\actsmart\Interpreters\Slack\SlackMessageInterpreter;

class GenericSlackController extends ActiveController
{
    const SLACK_ACTION_TYPE_BUTTON = 'button';
    const SLACK_ACTION_TYPE_MENU = 'menu';

    protected $slack_verification_token;

    protected $interpreter;

    public function __construct(Agent $agent, $slack_verification_token)
    {
        parent::__construct($agent);
        $this->slack_verification_token = $slack_verification_token;
        $this->interpreter = new SlackMessageInterpreter();
    }
}