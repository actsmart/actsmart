<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * Class FacebookMessage
 * @package actsmart\actsmart\Actuators\Facebook
 *
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages#send_api_basics
 */
class FacebookMessage
{
    /** Message types @see https://developers.facebook.com/docs/messenger-platform/send-messages#messaging_types */
    const RESPONSE = 'RESPONSE';
    const UPDATE = 'UPDATE';
    const MESSAGE_TAG = 'MESSAGE_TAG';

    /** The message text. */
    private $text = null;

    /** Currently only used for message updates */
    private $response_url;

    /** The id of the recipient of the message */
    private $recipientId;

    /** @var string Defines the type of message we are sending */
    private $messageType = self::RESPONSE;

    public function __construct($userId)
    {
        $this->recipientId = $userId;
    }

    /**
     * Sets text for a standard Facebook message. The main text is escaped
     *
     * @param $format - main message text
     * @param array $args - replaced in format
     * @return $this
     */
    public function setText($format, $args = [])
    {
        // Escape &, <, > characters
        $this->text = vsprintf(htmlspecialchars($format, ENT_NOQUOTES), $args);
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return mixed
     */
    public function getResponseUrl()
    {
        return $this->response_url;
    }

    /**
     * @param mixed $response_url
     */
    public function setResponseUrl($response_url)
    {
        $this->response_url = $response_url;
    }

    public function getMessageToPost()
    {
        $message = [
            'messaging_type' => $this->messageType,
            'recipient' => [
                'id' => $this->recipientId
            ],
            'message' => [
                'text' => $this->getText()
            ]
        ];

        return $message;
    }
}
