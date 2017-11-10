<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Sensors\Slack\SlackInteractiveMessageEvent;

/**
 * When a user interacts with an action on a slack message attachement
 * we can reply with an updated message to display changes to the user. This
 * class provides functionality to "redraw" the original message and change it
 * as required.
 *
 * Slack matches the message to be updated through the message timestamp/
 *
 * Class SlackUpdateMessage
 * @package actsmart\actsmart\Actuators\Slack
 */
class SlackUpdateMessage extends SlackMessage
{

    /** @var String - timestamp of the message to be updated **/
    private $ts;

    public function __construct($token, $channel, $type, $ts)
    {
        parent::__construct($token, $channel, $type);
        $this->ts = $ts;
    }

    /**
     * @return string
     */
    public function getTs()
    {
        return $this->ts;
    }

    /**
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

    /**
     * Rebuilds the original message this message is supposed to update.
     *
     * @param SlackInteractiveMessageEvent $e
     */
    public function rebuildOriginalMessage(SlackInteractiveMessageEvent $e)
    {
        foreach ($e->getAttachments() as $attachment) {
            $new_attachment = new SlackMessageAttachment();
            $new_attachment->rebuildAttachment($attachment);
            $this->addAttachment($new_attachment);
        }
    }

    /**
     * Removes an action based on the action value.
     * @param $value
     * @return mixed
     */
    public function removeAction($value)
    {
        foreach ($this->getAttachments() as $attachment) {
            if ($attachment->removeAction($value)) {
                return $attachment;
            }
        }
    }

    /**
     * Removes an action and replaces it with a field.
     * @param $value
     * @param $field
     */
    public function removeActionReplaceWithField($value, $field)
    {
        $attachment = $this->removeAction($value);
        $attachment->addField($field);
    }
}
