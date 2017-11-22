<?php

namespace actsmart\actsmart\Interpreters;

use Symfony\Component\EventDispatcher\GenericEvent;

interface InterpreterInterface
{
    public function interpret(GenericEvent $e);
}
