<?php

namespace actsmart\actsmart\Actuators\Slack;

/**
 * Class SlackMessage
 * @package actsmart\actsmart\Actuators\Slack
 *
 * @see https://api.slack.com/methods/chat.postMessage
 */
class SlackStandardMessage extends SlackMessage
{
    private $reply_broadcast = 'full';

    private $thread_is = null;

    private $unfurl_link = true;

    private $username = null;

    private $replace_original = false;

    private $delete_original = false;
}
