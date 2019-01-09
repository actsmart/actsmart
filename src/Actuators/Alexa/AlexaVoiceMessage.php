<?php

namespace actsmart\actsmart\Actuators\Alexa;

/**
 * A default Alexa voice message response
 *
 * @see https://developer.amazon.com/docs/custom-skills/request-and-response-json-reference.html#outputspeech-object
 */
class AlexaVoiceMessage implements AlexaResponse
{
    private $message;

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    public function getMessageToPost()
    {
        $message = [
            self::RESPONSE =>[
                self::OUTPUT_SPEECH => [
                    self::TYPE => self::SSML,
                    'ssml' => "<speak>{$this->getMessage()}</speak>",
                ]
            ],
        ];

        return $message;
    }
}
