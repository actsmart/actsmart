<?php

namespace actsmart\actsmart\Actuators\Slack;

class SlackEphemeralMessage extends SlackMessage
{

    // The user this ephemeral message should appear to
    private $user;

    public function __construct($token, $channel, $type, $user)
    {
        parent::__construct($token, $channel, $type);
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
            'token' => $this->getToken(),
            'channel' => $this->getChannel(),
            'text' => $this->getText(),
            'as_user' => $this->sendingAsUser(),
            'user' => $this->getUser(),
            'attachments' => $this->getAttachmentsToPost(),
        ];
        return $form_params;
    }
}
