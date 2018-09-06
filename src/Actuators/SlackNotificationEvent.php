<?php

namespace actsmart\actsmart\Actuators;

use Symfony\Component\EventDispatcher\GenericEvent;

class SlackNotificationEvent extends GenericEvent
{
    protected $event_key;

    public function __construct($subject = null, array $arguments = array(), $event_key = 'event.action.slacknotification')
    {
        parent::__construct($subject, $arguments);
        $this->event_key = $event_key;
    }

    public function getKey()
    {
        return $this->event_key;
    }
}
