<?php
namespace actsmart\actsmart\Sensors;

use Symfony\Component\EventDispatcher\GenericEvent;

class ReadMessageEvent extends GenericEvent
{
    const EVENT_NAME = 'event.webchat.action';

    private $messageId;
    private $userId;

    public function __construct($subject = null, array $arguments = array())
    {
        parent::__construct($subject, $arguments);
        $this->messageId = isset($arguments['message_id']) ? $arguments['message_id'] : null;
        $this->userId = isset($arguments['user_id']) ? $arguments['user_id'] : null;
        $this->author = isset($arguments['author']) ? $arguments['author'] : null;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return mixed
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * @param $messageId
     */
    public function setMessageId($messageId): void
    {
        $this->messageId = $messageId;
    }

    /**
     * @param $userId
     */
    public function setUserId($userId): void
    {
        $this->userId = $userId;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }
}
