<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 14:36
 */

namespace actsmart\actsmart\Controllers\Slack;

use Symfony\Component\HttpFoundation\Response;
use actsmart\actsmart\Agent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Controllers\Active\ActiveController;
use actsmart\actsmart\Interpreters\Slack\SlackMessageInterpreter;

class MessageController extends ActiveController
{
    private $slack_verification_token;

    /** @var  Agent */
    private $agent;

    private $interpreter;

    public function __construct(Agent $agent, $slack_verification_token)
    {
        $this->agent = $agent;
        $this->slack_verification_token = $slack_verification_token;
        $this->interpreter = new SlackMessageInterpreter();

    }

    public function execute(SensorEvent $e = null)
    {
        if ($e->getSubject() == 'message') {
            if ($e->getArgument('token') == $this->slack_verification_token) {
                $response = $this->interpreter->interpret($e);
                // Once a response is translated to an intent, review context and instantiate a plan
                if ($response) {
                    $this->actuators['slack.actuator']->act($response);
                } else {}
            }
        }
    }

}