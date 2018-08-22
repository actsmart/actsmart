<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Actuators\Slack\SlackMessageAttachment;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\RegularExpressionHelper;

class MessageEvent extends WebChatEvent implements UtteranceEvent
{
    use RegularExpressionHelper;

    const EVENT_NAME = 'event.webchat.message';

    private $user_id = null;

    private $timestamp = null;

    private $text = null;

    private $attachments = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = []);

        // TODO pull the values out of the message
        $this->user_id = $subject->user_id ?? null;
        $this->timestamp = null;
        $this->text = $subject->data->text ?? null;
        $this->attachments = null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getSubject()->data->text;
    }

    public function mentions($user_id)
    {
        return $this->userNameMentioned($this->getUtterance(), $user_id);
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @return mixed
     */
    public function getTextMessage()
    {
        return $this->text;
    }

    /**
     * @return SlackMessageAttachment[]
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
