<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class SlackDialogSubmissionEvent extends SlackEvent implements UtteranceEvent
{
    const EVENT_NAME = 'event.slack.dialog_submission';

    private $workspace_id;

    private $channel_id;

    private $callback_id;

    private $response_url;

    private $user_id;

    private $submission;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);
        $this->workspace_id = $subject->team->id ?? null;
        $this->callback_id = $subject->callback_id ?? null;
        $this->channel_id = $subject->channel->id ?? null;
        $this->response_url = $subject->response_url ?? null;
        $this->user_id = $subject->user->id ?? null;
        $this->submission = $subject->submission ?? null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::SLACK_DIALOG_SUBMISSION);
        $utterance->put(Literals::TEXT, '');
        $utterance->put(Literals::WORKSPACE_ID, $this->getWorkspaceId());
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::CHANNEL_ID, $this->getChannelId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::CALLBACK_ID, $this->getCallbackId());
        $utterance->put(Literals::ITEM_ID, $this->getItemId());
        $utterance->put(Literals::ACTION, $this->getAction());
        $utterance->put(Literals::SUBMISSION, $this->getSubmission());
        $utterance->put(Literals::RESPONSE_URL, $this->getResponseUrl());
        return $utterance;
    }

    /**
     * @return string | null
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }

    /**
     * @return string | null
     */
    public function getChannelId()
    {
        return $this->channel_id;
    }

    /**
     * @return string | null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getSubmission()
    {
        return $this->submission;
    }

    /**
     * @return string | null
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return mixed
     */
    public function getResponseUrl()
    {
        list($action, $item_id, $timestamp, $response_url) = array_pad(explode(';', $this->callback_id), 4, null);

        return (substr($response_url, 0, 4) == 'url:') ? substr($response_url, 4) : $this->response_url;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        list($action, $item_id, $timestamp, $response_url) = array_pad(explode(';', $this->callback_id), 4, null);

        return (substr($action, 0, 2) == 'a:') ? substr($action, 2) : '';
    }

    /**
     * @return mixed
     */
    public function getItemId()
    {
        list($action, $item_id, $timestamp, $response_url) = array_pad(explode(';', $this->callback_id), 4, null);

        return (substr($item_id, 0, 3) == 'id:') ? substr($item_id, 3) : '';
    }

    /**
     * Returns the time the command was called.
     *
     * @return int
     */
    public function getTimestamp()
    {
        list($action, $item_id, $timestamp, $response_url) = array_pad(explode(';', $this->callback_id), 4, null);

        return (substr($timestamp, 0, 3) == 'ts:') ? substr($timestamp, 3) : time();
    }
}
