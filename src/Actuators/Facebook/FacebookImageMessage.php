<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages
 */
class FacebookImageMessage extends FacebookMessage
{
    private $imageUrl;

    public function setImageUrl($imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    public function getImageUrl()
    {
        return $this->imageUrl;
    }

    public function getMessageToPost()
    {
        $message = [
            'recipient' => [
                'id' => $this->getRecipientId()
            ],
            'message' => [
                'attachment' => [
                    'type' => 'image',
                    "payload" => [
                        "url" => $this->getImageUrl(),
                        "is_reusable" => false
                    ]
                ]
            ]
        ];

        return $message;
    }
}
