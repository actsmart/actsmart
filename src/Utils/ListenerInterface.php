<?php

namespace actsmart\actsmart\Utils;

use Symfony\Component\EventDispatcher\GenericEvent;

interface ListenerInterface
{
    public function listen(GenericEvent  $e);

    public function listensForEvents();

    public function getPriority();

    public function setPriority($p);
}
