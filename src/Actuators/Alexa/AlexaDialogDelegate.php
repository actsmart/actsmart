<?php

namespace actsmart\actsmart\Actuators\Alexa;

/**
 * Delegate the conversation flow to Alexa to build the intent's slots
 *
 * @see https://developer.amazon.com/docs/custom-skills/dialog-interface-reference.html#scenario-delegate
 */
class AlexaDialogDelegate implements AlexaResponse
{
    const DIALOG_DELEGATE = 'Dialog.Delegate';

    public function getMessageToPost()
    {
        $message = [
            self::RESPONSE =>[
                self::DIRECTIVES => [
                    [self::TYPE => self::DIALOG_DELEGATE],
                ]
            ],
        ];

        return $message;
    }
}
