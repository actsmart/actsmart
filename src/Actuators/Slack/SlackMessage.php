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
    private $token;

    private $channel;

    private $text = null;

    private $as_user = false;

    private $attachments= [];

    private $icon_emoji = null;

    private $icon_url = null;

    private $link_names = true;

    /* @see https://api.slack.com/docs/message-formatting#message_formatting */
    private $parse = 'none';

    private $reply_broadcast = 'full';

    private $thread_is = null;

    private $unfurl_link = true;

    private $username = null;

    private $replace_original = false;

    private $delete_original = false;

    public function __construct($token, $channel)
    {
        $this->token = $token;
        $this->channel = $channel;
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
        $this->text = htmlspecialchars($text, [ENT_NOQUOTES]);
        return $this;
    }

    public function getText()
    {
        return $this->text;
    }

    public function sendAsUser($as_user)
    {
        $this->as_user = $as_user;
        return $this;
    }

    public function sendingAsUser(){
        return $this->as_user;
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
        $form_params = [
            'token' => $this->getToken(),
            'channel' => $this->getChannel(),
            'text' => $this->getText(),
            'as_user' => $this->sendingAsUser(),
            'attachments' => json_encode($this->getAttachmentsToPost()),
        ];
        return $form_params;
    }
}