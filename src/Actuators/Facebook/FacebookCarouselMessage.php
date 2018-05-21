<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages/template/generic
 */
class FacebookCarouselMessage extends FacebookMessage
{
    private $elements;

    public function getElements()
    {
        return $this->elements;
    }

    public function addElement(FacebookElement $element)
    {
        $this->elements[] = $element;
    }

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
                        "elements" => $this->getElements()
                    ]
                ]
            ]
        ];

        return $message;
    }
}
