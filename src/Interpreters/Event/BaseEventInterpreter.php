<?php

namespace actsmart\actsmart\Interpreters\Event;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class BaseEventInterpreter implements LoggerAwareInterface, ComponentInterface, NotifierInterface, ListenerInterface
{
    use ComponentTrait, LoggerAwareTrait, NotifierTrait, ListenerTrait;
}
