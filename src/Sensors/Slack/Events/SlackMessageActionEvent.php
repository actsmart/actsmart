<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\RegularExpressionHelper;

class SlackMessageActionEvent extends SlackEvent implements UtteranceEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.slack.messageaction';

    private $workspace_id;

    private $channel_id;

    private $channel_name;

    private $callback_id;

    private $response_url;

    private $team_domain;

    private $text;

    private $token;

    private $trigger_id;

    private $user_id;

    private $username;


    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments);
        $this->workspace_id = $subject->team->id ?? null;
        $this->channel_id = $subject->channel->id ?? null;
        $this->channel_name = $subject->chanel->name ?? null;
        $this->callback_id = $subject->callback_id ?? null;
        $this->response_url = $subject->response_url ?? null;
        $this->team_domain =  $subject->team->domain ?? null;
        $this->text = $subject->message->text ?? null;
        $this->token = $subject->token ?? null;
        $this->trigger_id = $subject->trigger_id ?? null;
        $this->user_id = $subject->user->id ?? null;
        $this->username = $subject->user->name ?? null;
    }

    public function getKey()
    {
        return SELF::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getCallbackId();
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
    public function getChannelName()
    {
        return $this->channel_name;
    }

    /**
     * @return string | null
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @return string | null
     */
    public function getResponseUrl()
    {
        return $this->response_url;
    }

    /**
     * @return string | null
     */
    public function getTeamDomain()
    {
        return $this->team_domain;
    }

    /**
     * @return string | null
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string | null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return string | null
     */
    public function getTriggerId()
    {
        return $this->trigger_id;
    }

    /**
     * @return string | null
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return string | null
     */
    public function getUserName()
    {
        return $this->username;
    }

    /**
     * Returns the time the command was called.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return time();
    }
}
