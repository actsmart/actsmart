<?php
namespace actsmart\actsmart\Sensors;

use Symfony\Component\EventDispatcher\GenericEvent;

class ReadMessageEvent extends GenericEvent
{
    const EVENT_NAME = 'event.webchat.action';

    private $messageId;

    public function __construct($subject = null, array $arguments = array())
    {
        parent::__construct($subject, $arguments);
        $this->messageId = isset($arguments['message_id']) ? $arguments['message_id'] : null;
    }

    /**
     * @return mixed
     */
    public function getMessageId()
    {
        return $this->messageId;
    }

    /**
     * @param mixed $message_id
     */
    public function setMessageId($messageId): void
    {
        $this->messageId = $messageId;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }
}
