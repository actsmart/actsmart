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
        $this->setText('Change is awesome');

        foreach ($e->getAttachments() as $attachment)
        {
            $new_attachment = new SlackMessageAttachment();
            $new_attachment->rebuildAttachment($attachment);
            $this->addAttachment($new_attachment);
        }
    }

    public function removeAction($value) {
        foreach ($this->getAttachments() as $attachment)
        {
            if ($attachment->removeAction($value)) return $attachment;
        }
    }

    public function removeActionReplaceWithField($value, $field)
    {
        $attachment = $this->removeAction($value);
        $attachment->addReadyField($field);
    }

}