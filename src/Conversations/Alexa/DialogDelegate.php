<?php

namespace actsmart\actsmart\Conversations\Alexa;

use actsmart\actsmart\Actuators\Alexa\AlexaDialogDelegate;
use actsmart\actsmart\Actuators\Alexa\AlexaResponse;
use actsmart\actsmart\Conversations\Message;

class DialogDelegate extends Message implements AlexaResponse
{
    /**
     * @param null $actionData
     * @param null $informationResponse
     * @return AlexaDialogDelegate
     */
    public function getAlexaResponse($actionData = null, $informationResponse = null)
    {
        return new AlexaDialogDelegate();
    }
}
