<?php

namespace actsmart\actsmart\Conversations;

use Symfony\Component\EventDispatcher\GenericEvent;

interface ConditionInterface
{
    public function check(GenericEvent $e);

}
