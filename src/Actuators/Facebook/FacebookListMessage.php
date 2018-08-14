<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/reference/template/list/
 */
class FacebookListMessage extends FacebookMessage
{
    use HasElements;

    use HasButtons;

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
                        "top_element_style" => "compact",
                        "template_type" => 'list',
                        "elements" => $this->getElements()
                    ]
                ]
            ]
        ];

        return $message;
    }
}
