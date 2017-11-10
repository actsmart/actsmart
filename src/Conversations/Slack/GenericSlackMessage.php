<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Sensors\Slack\SlackEvent;
use actsmart\actsmart\Actuators\Slack\SlackStandardMessage;
use actsmart\actsmart\Conversations\SlackResponseInterface;


class GenericSlackMessage extends Message implements SlackMessageInterface
{
    public function getSlackResponse(SlackEvent $e)
    {
        $message = new SlackStandardMessage($e->getSubject()->event->channel);
        $message->setText($this->getTextResponse());
        return $message;
    }

}