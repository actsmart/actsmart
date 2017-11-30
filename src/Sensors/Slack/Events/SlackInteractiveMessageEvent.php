<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

class SlackInteractiveMessageEvent extends SlackEvent
{
    const EVENT_NAME = 'event.slack.interactive_message';

    private $callback_id;

    private $trigger_id;

    private $workspace_id;

    private $user_id;

    private $timestamp;

    private $channel_id;

    private $response_url;

    private $attachments;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);
        $this->callback_id = $subject->callback_id;
        $this->trigger_id = $subject->trigger_id;

        $this->workspace_id = $subject->team->id;
        $this->user_id = $subject->user->id;
        $this->timestamp = $subject->message_ts;
        $this->channel_id = $subject->channel->id;
        $this->response_url = $subject->response_url;
        $this->attachments = isset($subject->original_message->attachments) ? $subject->original_message->attachments : null;
    }

    public function getUtterance()
    {
        return $this->callback_id;
    }


    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    /**
     * @return mixed
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return mixed
     */
    public function getTriggerId()
    {
        return $this->trigger_id;
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
     * @return mixed
     */
    public function getResponseUrl()
    {
        return $this->response_url;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function getActionPerformed()
    {
        // @todo assuming just one and always in an interactive message
        return $this->getSubject()->actions[0];
    }

    public function getActionPerformedValue()
    {
        return $this->getSubject()->actions[0]->value;
    }

    public function getTextMessage()
    {
        return $this->getSubject()->original_message->text;
    }
}
