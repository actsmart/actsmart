<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Actuators\Slack\SlackMessageAttachment;
use actsmart\actsmart\Sensors\UtteranceEvent;

class ActionEvent extends WebChatEvent implements UtteranceEvent
{
    const EVENT_NAME = 'event.webchat.action';

    private $callback_id = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = []);

        $this->callback_id = $subject->callback_id ?? null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    public function getUtterance()
    {
        return $this->getSubject()->data->text;
    }

    public function getCallbackId()
    {
        return $this->callback_id;
    }
}
