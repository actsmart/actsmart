<?php

namespace actsmart\actsmart\Sensors\Slack\Events;


use actsmart\actsmart\Actuators\Slack\SlackMessageAttachment;

abstract class SlackRebuildableMessageEvent extends SlackEvent
{
    /**
     * Default response for getting text message that should be overwritten by specific events
     * @return string
     */
    abstract function getTextMessage();

    /**
     * Default response for getting attachments that should be overwritten by specific events
     *
     * @return SlackMessageAttachment[]
     */
    abstract function getAttachments();
}
