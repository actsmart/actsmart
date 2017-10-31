<?php

namespace actsmart\actsmart\Conversations;

/**
 * Class Message
 * @package actsmart\actsmart\Conversations
 *
 * A message is a structured piece of information that an Utterance will
 * carry from one conversation participant in a scene to another.
 *
 * @todo - we probably want to bring the Slack Message structure here.
 */
class Message
{
    public $text_response;

    public function __construct($text_response = null)
    {
        $this->text_response = $text_response;
    }

    public function getTextResponse()
    {
        return $this->text_response;
    }
}