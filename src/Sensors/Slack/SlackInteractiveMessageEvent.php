<?php

namespace actsmart\actsmart\Sensors\Slack;

use actsmart\actsmart\Sensors\SensorEvent;

class SlackInteractiveMessageEvent extends SlackEvent
{
    const EVENT_NAME = 'slack.interactive_message';

    private $callback_id;

    private $trigger_id;

    private $workspace_id;

    private $user_id;

    private $timestamp;

    private $channel_id;

    private $response_url;

    private $attachments;

    public function __construct($type, $message)
    {
        parent::__construct($type, $message);
        $this->callback_id = $message->callback_id;
        $this->trigger_id = $message->trigger_id;

        $this->workspace_id = $message->team->id;
        $this->user_id = $message->user->id;
        $this->timestamp = $message->message_ts;
        $this->channel_id = $message->channel->id;
        $this->response_url = $message->response_url;
        $this->attachments = isset($message->original_message->attachments) ? $message->original_message->attachments : null;
    }

    public function getUtterance()
    {
        return $this->callback_id;
    }


    public function getName()
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
        return $this->getMessage()->actions[0];
    }

    public function getTextMessage()
    {
        return $this->getMessage()->original_message->text;
    }


}