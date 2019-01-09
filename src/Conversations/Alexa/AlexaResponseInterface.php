<?php

namespace actsmart\actsmart\Conversations\Alexa;

use actsmart\actsmart\Actuators\Alexa\AlexaResponse;

interface AlexaResponseInterface
{
    /**
     * @param null $action_data
     * @return AlexaResponse
     */
    public function getAlexaResponse($action_data = null);
}
