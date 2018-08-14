<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages/template/generic
 */
class FacebookCarouselMessage extends FacebookMessage
{
    use HasElements;

    public function getMessageToPost()
    {
        $message = [
            'recipient' => [
                'id' => $this->getRecipientId()
            ],
            'message' => [
                'attachment' => [
                    'type' => 'template',
                    "payload" => [
                        "template_type" => 'generic',
                        "elements" => json_encode($this->getElements())
                    ]
                ]
            ]
        ];

        return $message;
    }
}
