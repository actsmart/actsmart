<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Actuators\Slack\SlackStandardMessage;


class GenericSlackMessage extends Message implements SlackResponseInterface
{
    /**
     * This extends the default Conversation Message object to inject slack-specific support
     * for generic text message.
     *
     * @param string $channel
     * @param string $workspace
     * @param mixed $action_data
     * @return SlackStandardMessage
     */
    public function getSlackResponse(string $channel, string $workspace, $action_data = null)
    {
        $message = new SlackStandardMessage($channel, $workspace);
        $message->setText($this->getTextResponse());
        return $message;
    }
}