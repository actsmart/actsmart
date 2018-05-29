<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\RegularExpressionHelper;

class SlackMessageEvent extends SlackEvent implements UtteranceEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.slack.message';

    private $workspace_id = null;

    private $user_id = null;

    private $timestamp = null;

    private $channel_id = null;

    /**
     * The type of channel that the event originated from - https://api.slack.com/events/message.channels
     * @var null
     */
    private $channel_type = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);

        $this->workspace_id = isset($subject->team_id) ? $subject->team_id : null;
        $this->user_id = isset($subject->event->user) ? $subject->event->user : null;
        $this->timestamp= isset($subject->event_time) ? $subject->event_time : null;
        $this->channel_id = isset($subject->event->channel) ? $subject->event->channel : null;
        $this->channel_type = isset($subject->event->channel_type) ? $subject->event->channel_type : null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getSubject()->event->text;
    }

    public function mentions($user_id)
    {
        return $this->userNameMentioned($this->getUtterance(), $user_id);
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

    /**
     * @return null
     */
    public function getChannelType()
    {
        return $this->channel_type;
    }
}
