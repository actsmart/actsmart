<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Actuators\Slack\SlackStandardMessage;


class GenericSlackMessage extends Message implements SlackResponseInterface
{
    public function getSlackResponse(SensorEvent $e)
    {
        $message = new SlackStandardMessage($e->getChannelId());
        $message->setText($this->getTextResponse());
        return $message;
    }

}