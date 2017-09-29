<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/09/2017
 * Time: 17:23
 */

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

    private $text;

    private $as_user = false;

    private $attachments= [];

    private $icon_emoji;

    private $icon_url;

    private $link_names = true;

    /* @see https://api.slack.com/docs/message-formatting#message_formatting */
    private $parse = 'none';

    private $reply_broadcast = 'full';

    private $thread_is = '';

    private $unfurl_link = true;

    private $username = '';

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

    public function addAttachment(SlackMessageAttachmeent $attachment)
    {
        $this->attachments[] = $attachment;
        return $this;
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
        foreach ($lines as $line){
            $message .= $line->name . "\n";
        }

        if ($set_as_text) {
            $this->text = $message;
        }
        return $message;
    }



}