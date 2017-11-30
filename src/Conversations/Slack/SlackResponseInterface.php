<?php

namespace actsmart\actsmart\Conversations\Slack;

interface SlackResponseInterface
{
    public function getSlackResponse(string $channel, string $workspace, $action_data = null);
}