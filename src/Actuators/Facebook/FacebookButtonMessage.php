<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * A structured message that includes text and buttons
 *
 * @see https://developers.facebook.com/docs/messenger-platform/reference/template/button
 */
class FacebookButtonMessage extends FacebookMessage
{
    use HasButtons;

    public function getMessageToPost()
    {
        $message = [
            'recipient' => [
                'id' => $this->getRecipientId()
            ],
            'message' => [
                "attachment" => [
                    "type" => "template",
                    "payload" => [
                        "template_type" => "button",
                        "text" => $this->getText(),
                        "buttons" => $this->buttons
                    ]

                ]
            ]
        ];

        return $message;
    }
}
