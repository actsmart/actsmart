<?php

namespace actsmart\actsmart\Conversations\Alexa;

use actsmart\actsmart\Actuators\Alexa\AlexaVoiceMessage;
use actsmart\actsmart\Conversations\Message;

class GenericAlexaMessage extends Message implements AlexaResponseInterface
{
    /**
     * @param null $actionData
     * @param null $informationResponse
     * @return AlexaVoiceMessage
     */
    public function getAlexaResponse($actionData = null, $informationResponse = null)
    {
        $message = new AlexaVoiceMessage();
        $message->setMessage($this->getTextResponse());

        return $message;
    }
}
