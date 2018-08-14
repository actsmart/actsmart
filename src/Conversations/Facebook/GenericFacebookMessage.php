<?php

namespace actsmart\actsmart\Conversations\Facebook;

use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Actuators\Slack\SlackStandardMessage;

class GenericFacebookMessage extends Message implements FacebookResponseInterface
{
    /**
     * This extends the default Conversation Message object to inject facebook-specific support
     * for generic text message.
     *
     * @param string $userId - the user to post the message to
     * @return SlackStandardMessage
     */
    public function getFacebookResponse(string $userId)
    {
        $message = new SlackStandardMessage($userId);
        $message->setText($this->getTextResponse());
        return $message;
    }
}
