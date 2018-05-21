<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages/template/generic
 */
class FacebookListMessage extends FacebookMessage
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
                    "top_element_style" => "LARGE",
                    "payload" => [
                        "template_type" => 'list',
                        "elements" => $this->getElements()
                    ]
                ]
            ]
        ];

        return $message;
    }
}
