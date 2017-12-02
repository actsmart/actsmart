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
     * @param string $channel - the channel to post the message.
     * @param string $workspace - the workspace to post the message in.
     * @param mixed $action_data - the result of an action that can be used to format a message.
     * @return SlackStandardMessage
     */
    public function getSlackResponse(string $channel, string $workspace, $action_data = null)
    {
        $message = new SlackStandardMessage($channel, $workspace);
        $message->setText($this->getTextResponse());
        return $message;
    }
}