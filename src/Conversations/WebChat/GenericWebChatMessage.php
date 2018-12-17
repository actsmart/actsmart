<?php

namespace actsmart\actsmart\Conversations\WebChat;

use actsmart\actsmart\Actuators\WebChat\WebChatMessage;
use actsmart\actsmart\Conversations\Message;

class GenericWebChatMessage extends Message implements WebChatResponseInterface
{
    /**
     * @param null $action_data
     * @return WebChatMessage
     */
    public function getWebChatResponse($action_data = null, $informationResponse = null)
    {
        $message = new WebChatMessage();
        $message->setText($this->getTextResponse());
        return $message;
    }
}
