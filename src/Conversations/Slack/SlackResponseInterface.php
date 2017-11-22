<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

interface SlackResponseInterface
{
    public function getSlackResponse(SensorEvent $e, $action_result = null);
}