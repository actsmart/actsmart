<?php

namespace actsmart\actsmart\Actuators\Slack;

/**
 * Class SlackMessage
 * @package actsmart\actsmart\Actuators\Slack
 *
 * @see https://api.slack.com/methods/chat.postMessage
 */
class SlackMessage
{
    /* Channel in which to post the message. */
    private $channel;

    /* Workspace the channel belongs to. */
    private $workspace;

    /* The message text. */
    private $text = null;

    /* Whether to post the message as the authenticated user or as a bot. */
    private $as_user = false;

    /* Attachments to the message see SlackMEssageAttachment. */
    private $attachments= [];

    /* Whether to find and link names and channels that may appear in the message. */
    private $link_names = true;

    /* @see https://api.slack.com/docs/message-formatting#message_formatting */
    private $parse = 'none';

    /* Currently only used for message updates */
    private $response_url;


    public function __construct($channel, $workspace)
    {
        $this->channel = $channel;
        $this->workspace = $workspace;
    }

    public function setChannel($channel)
    {
        $this->channel = $channel;
        return $this;
    }

    public function getChannel()
    {
        return $this->channel;
    }

    /**
     * @return mixed
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param mixed $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
    }

    /**
     * Sets text for a standard slack message. The main text is escaped as per Slack API requirements
     * https://api.slack.com/docs/message-formatting#how_to_escape_characters
     *
     * @param $format - main message text
     * @param array $args - replaced in format
     * @return $this
     */
    public function setText($format, $args = array())
    {
        // Escape &, <, > characters
        $this->text = vsprintf(htmlspecialchars($format, ENT_NOQUOTES), $args);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param $as_user
     * @return $this
     */
    public function sendAsUser($as_user)
    {
        $this->as_user = $as_user;
        return $this;
    }

    /**
     * @return bool
     */
    public function sendingAsUser()
    {
        return $this->as_user;
    }

    /**
     * @return mixed
     */
    public function getResponseUrl()
    {
        return $this->response_url;
    }

    /**
     * @param mixed $response_url
     */
    public function setResponseUrl($response_url)
    {
        $this->response_url = $response_url;
    }

    public function addAttachment(SlackMessageAttachment $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }

    public function hasAttachments()
    {
        if (count($this->attachments) >= 1) {
            return true;
        }

        return false;
    }

    public function getAttachmentsToPost()
    {
        $attachments_to_post = [];
        foreach ($this->attachments as $attachment) {
            $attachments_to_post[] = $attachment->getAttachmentToPost();
        }
        return $attachments_to_post;
    }

    /**
     * Helper function to create a multiline message (mostly to ensure we don't forget to user
     * double quotes appropriately).
     *
     * @param $lines
     * @param $set_as_text - set to true to set the message text to the result
     * @return string
     */
    public function createMultilineTextMessage($lines, $set_as_text = false)
    {
        $message = "";
        foreach ($lines as $line) {
            $message .= $line->name . "\n";
        }

        if ($set_as_text) {
            $this->text = $message;
        }
        return $message;
    }

    public function getMessageToPost()
    {
        $message = [
            'channel' => $this->getChannel(),
            'text' => $this->getText(),
            'as_user' => $this->sendingAsUser(),
            'attachments' => $this->getAttachmentsToPost(),
        ];

        return $message;
    }
}
