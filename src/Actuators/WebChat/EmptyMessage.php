<?php

namespace actsmart\actsmart\Actuators\WebChat;

/**
 * Class EmptyMessage
 *
 * An empty message will cause the actuator to just send an HTTP 200 with no content.
 *
 * Please do not use as part of an array of messages.
 *
 * @package actsmart\actsmart\Actuators\WebChat
 */
class EmptyMessage extends WebChatMessage
{
    public function __construct()
    {
        parent::__construct();
        $this->setAsEmpty();
    }
}