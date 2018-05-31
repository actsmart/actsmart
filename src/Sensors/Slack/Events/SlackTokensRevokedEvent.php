<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

class SlackTokensRevokedEvent extends SlackEvent
{
    const EVENT_NAME = 'event.slack.tokens_revoked';

    private $workspace_id = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);

        $this->workspace_id = isset($subject->team_id) ? $subject->team_id : null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    /**
     * @return mixed
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }
}
