<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use Illuminate\Support\Facades\Log;

class SlackMessageEvent extends SlackEvent implements UtteranceEvent
{
    const EVENT_NAME = 'event.slack.message';

    private $workspace_id = null;

    private $user_id = null;

    private $timestamp = null;

    private $channel_id = null;

    public function __construct($subject, $arguments)
    {
        parent::__construct($subject, $arguments);

        $this->workspace_id = isset($subject->team_id) ? $subject->team_id : null;
        $this->user_id = isset($subject->event->user) ? $subject->event->user : null;
        $this->timestamp= isset($subject->event_time) ? $subject->event_time : null;
        $this->channel_id = isset($subject->event->channel) ? $subject->event->channel : null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getArgument('event')->text;
    }

    public function mentions($user_id)
    {
        if (strpos($this->getUtterance(), $user_id)) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channel_id;
    }
}
