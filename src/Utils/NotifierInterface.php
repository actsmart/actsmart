<?php

namespace actsmart\actsmart\Utils;

use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\EventDispatcher\EventDispatcher;

interface NotifierInterface
{
    public function notify($event_key, GenericEvent $event);

    public function setDispatcher(EventDispatcher $dispatcher);
}
