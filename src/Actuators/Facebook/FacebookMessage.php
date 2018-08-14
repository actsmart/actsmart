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

    /**
     * @return mixed
     */
    public function getRecipientId()
    {
        return $this->recipientId;
    }

    /**
     * @param mixed $recipientId
     */
    public function setRecipientId($recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    // TODO - all messages follow the same basic structure, so we shouldn't repeat this so much
    public function getMessageToPost()
    {
        $message = [
            'recipient' => [
                'id' => $this->getRecipientId()
            ],
            'message' => [
                'text' => $this->getText()
            ]
        ];

        return $message;
    }

    public function getTypingOnMessage()
    {
        return $this->getSenderAction('typing_on');
    }

    public function getTypingOffMessage()
    {
        return $this->getSenderAction('typing_off');
    }

    public function getMarkSeenMessage()
    {
        return $this->getSenderAction('mark_seen');
    }

    /**
     * @param $action
     * @return array
     */
    protected function getSenderAction($action): array
    {
        return [
            'recipient' => [
                'id' => $this->getRecipientId()
            ],
            'sender_action' => $action
        ];
    }
}
