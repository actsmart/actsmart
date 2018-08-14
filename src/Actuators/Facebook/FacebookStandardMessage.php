<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/reference/send-api
 */
class FacebookStandardMessage extends FacebookMessage
{
    const CONTENT_TYPE   = "content_type";
    const QUICK_REPLIES  = 'quick_replies';
    const MESSAGE        = 'message';

    /**
     * Quick replies can only be added to standard text messages
     * @see https://developers.facebook.com/docs/messenger-platform/send-messages/quick-replies
     */
    private $quickReplies = [];

    /**
     * @see https://developers.facebook.com/docs/messenger-platform/send-messages/quick-replies#locations
     */
    public function addGetLocationButton()
    {
        $this->quickReplies[] = [
            self::CONTENT_TYPE => 'location'
        ];
    }

    public function addEmailButton()
    {
        $this->quickReplies[] = [
            self::CONTENT_TYPE => 'user_email'
        ];
    }

    /**
     * Adds a text button with the given payload
     *
     * @param $title
     * @param $payload
     * @param null $imageUrl
     */
    public function addTextButton($title, $payload, $imageUrl = null)
    {
        $quickReply = [
            self::CONTENT_TYPE => "text",
            'title'            => $title,
            "payload"          => $payload,
        ];

        if ($imageUrl) {
            $quickReply['image_url'] = $imageUrl;
        }

        $this->quickReplies[] = $quickReply;
    }

    /**
     * Adds the quick replies to the message to post
     *
     * @return array
     */
    public function getMessageToPost()
    {
         $message = parent::getMessageToPost();
         if ($this->quickReplies) {
             $message[self::MESSAGE][self::QUICK_REPLIES] = $this->quickReplies;
         }
         return $message;
    }
}
