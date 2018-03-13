<?php

namespace actsmart\actsmart\Actuators\Slack;

/**
 * Class SlackEphemeralMessage
 * @package actsmart\actsmart\Actuators\Slack
 *
 * An ephemeral message is only shown to the a single user within a channel.
 */
class SlackEphemeralMessage extends SlackMessage
{

    // The user this ephemeral message should appear to
    private $user;

    public function __construct($channel, $user, $workspace)
    {
        parent::__construct($channel, $workspace);
        $this->user = $user;
    }

    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param mixed $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }


    public function getMessageToPost()
    {
        $form_params = [
            'channel' => $this->getChannel(),
            'text' => $this->getText(),
            'as_user' => $this->sendingAsUser(),
            'user' => $this->getUser(),
            'attachments' => $this->getAttachmentsToPost(),
        ];
        return $form_params;
    }
}
