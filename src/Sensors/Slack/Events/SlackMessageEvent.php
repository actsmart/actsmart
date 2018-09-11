<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Actuators\Slack\SlackMessageAttachment;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\RegularExpressionHelper;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class SlackMessageEvent extends SlackRebuildableMessageEvent implements UtteranceEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.slack.message';

    private $workspace_id = null;

    private $user_id = null;

    private $timestamp = null;

    private $channel_id = null;

    private $text = null;

    private $attachments = null;

    /**
     * The type of channel that the event originated from - https://api.slack.com/events/message.channels
     * @var null
     */
    private $channel_type = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, $this::EVENT_NAME);

        $this->workspace_id = isset($subject->team_id) ? $subject->team_id : null;
        $this->user_id = isset($subject->event->user) ? $subject->event->user : null;
        $this->timestamp = isset($subject->event_time) ? $subject->event_time : null;
        $this->text = isset($subject->event->text) ? $subject->event->text : null;
        $this->attachments = isset($subject->attachments) ? $subject->attachments : null;
        $this->channel_id = isset($subject->event->channel) ? $subject->event->channel : null;
        $this->channel_type = isset($subject->event->channel_type) ? $subject->event->channel_type : null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance()
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::SLACK_MESSAGE);
        $utterance->put(Literals::WORKSPACE_ID, $this->getWorkspaceId());
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::CHANNEL_ID, $this->getChannelId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        return $utterance;
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

    /**
     * @return mixed
     */
    public function getTextMessage()
    {
        return $this->text;
    }

    /**
     * @return SlackMessageAttachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
