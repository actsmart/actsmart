<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;

class SlackMessageEvent extends SlackEvent implements UtteranceEvent
{
    const EVENT_NAME = 'slack.message';

    private $workspace_id;

    private $user_id;

    private $timestamp;

    private $channel_id;

    public function __construct($type, $message)
    {
        parent::__construct($type, $message);

        $this->workspace_id = $message->team_id;
        $this->user_id = $message->event->user;
        $this->timestamp= $message->event_time;
        $this->channel_id = $message->event->channel;
    }

    public function getName()
    {
        return SELF::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getArgument('event')->text;
    }

    public function mentions($user_id)
    {
        if (strpos($this->getUtterance(), $user_id)) return true;
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