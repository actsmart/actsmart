<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Sensors\Slack\SlackInteractiveMessageEvent;

class SlackUpdateMessage extends SlackMessage
{

    // Timestamp of the message to be updated
    private $ts;

    public function __construct($token, $channel, $type, $ts)
    {
        parent::__construct($token, $channel, $type);
        $this->ts = $ts;
    }

    /**
     * @return mixed
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
     * @param mixed $user
     */
    public function setTs($ts)
    {
        $this->ts = $ts;
    }


    public function getMessageToPost()
    {
        $message = [
            'channel' => $this->getChannel(),
            'text' => $this->getText(),
            'as_user' => $this->sendingAsUser(),
            'ts' => $this->getTs(),
            'attachments' => $this->getAttachmentsToPost(),
        ];
        return $message;
    }

    public function rebuildOriginalMessage(SlackInteractiveMessageEvent $e)
    {

    }

}