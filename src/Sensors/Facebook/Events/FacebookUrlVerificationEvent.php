<?php

namespace actsmart\actsmart\Sensors\Facebook\Events;

class FacebookUrlVerificationEvent extends FacebookEvent
{
    const EVENT_KEY = 'event.facebook.url_verification';

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments, $this::EVENT_KEY);
    }

    public function getKey()
    {
        return $this->event_key;
    }
}
