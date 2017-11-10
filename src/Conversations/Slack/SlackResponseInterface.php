<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Sensors\Slack\SlackEvent;

interface SlackResponseInterface
{
    public function getSlackResponse(SlackEvent $e);
}