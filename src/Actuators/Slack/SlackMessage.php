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
    // Message, Ephemeral, Update
    // @todo - other ways to handle this as well - keeping it simple for now.
    private $type;

    private $token;

    private $channel;

    private $text = null;

    private $as_user = false;

    private $attachments= [];

    private $link_names = true;

    /* @see https://api.slack.com/docs/message-formatting#message_formatting */
    private $parse = 'none';

    /* Currently only used for message updates */
    private $response_url;


    public function __construct($token, $channel, $type)
    {
        $this->channel = $channel;
        $this->type = $type;
    }

    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    public function getToken()
    {
        return $this->token;
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

    public function setText($text)
    {
        // Escape &, <, > characters
        $this->text = htmlspecialchars($text, ENT_NOQUOTES);
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return SlackMessage
     */
    public function setType($type)
    {
        $this->type = $type;
        return $this;
    }

    public function sendAsUser($as_user)
    {
        $this->as_user = $as_user;
        return $this;
    }

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
