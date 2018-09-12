<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Actuators\Slack\SlackMessageAttachment;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class SlackInteractiveMessageEvent extends SlackRebuildableMessageEvent
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

    private $token;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);
        $this->callback_id = $subject->callback_id;
        $this->trigger_id = $subject->trigger_id;
        $this->token = $subject->token;
        $this->workspace_id = $subject->team->id;
        $this->user_id = $subject->user->id;
        $this->timestamp = $subject->message_ts;
        $this->channel_id = $subject->channel->id;
        $this->response_url = $subject->response_url;
        $this->attachments = isset($subject->original_message->attachments) ? $subject->original_message->attachments : null;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::SLACK_INTERACTIVE_MESSAGE);
        $utterance->put(Literals::TEXT, $this->getTextMessage());
        $utterance->put(Literals::WORKSPACE_ID, $this->getWorkspaceId());
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::CHANNEL_ID, $this->getChannelId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::CALLBACK_ID, $this->getCallbackId());
        $utterance->put(Literals::ACTION, $this->getActionName());
        $utterance->put(Literals::ACTION_PERFORMED_VALUE, $this->getActionPerformedValue());
        $utterance->put(Literals::RESPONSE_URL, $this->getResponseUrl());
        $utterance->put(Literals::TOKEN, $this->getToken());
        $utterance->put(Literals::TRIGGER_ID, $this->getTriggerId());
        $utterance->put(Literals::ATTACHMENTS, $this->getAttachments());
        return $utterance;
    }


    public function getKey()
    {
        return self::EVENT_NAME;
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

    /**
     * @return SlackMessageAttachment[].
     */
    public function getAttachments()
    {
        return $this->attachments;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Returns all the details associated with the action performed - typically name, type and value.
     * @return mixed
     */
    public function getActionPerformed()
    {
        // @todo assuming just one and always in an interactive message
        return $this->getSubject()->actions[0];
    }

    /**
     * Gets just the value associated with the action performed - assumes one selection or one action.
     * @return mixed
     */
    public function getActionPerformedValue()
    {
        if (isset($this->getSubject()->actions[0]->value)) {
            return $this->getSubject()->actions[0]->value;
        }

        if (isset($this->getSubject()->actions[0]->selected_options)) {
            return $this->getSubject()->actions[0]->selected_options[0]->value;
        }

        return null;
    }

    /**
     * The name of the action performed.
     * @return mixed
     */
    public function getActionName()
    {
        return $this->getSubject()->actions[0]->name;
    }

    /**
     * The text associated with the attachment that was the source of the action.
     *
     * @return mixed
     */
    public function getTextMessage()
    {
        return $this->getSubject()->original_message->text;
    }
}
