<?php

namespace actsmart\actsmart\Actuators;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class BaseActuator implements ComponentInterface, ActuatorInterface, NotifierInterface, LoggerAwareInterface
{
    use ComponentTrait, NotifierTrait, LoggerAwareTrait;

    CONST KEY = 'actuator.base';

    public function getKey()
    {
        return static::KEY;
    }

    /**
     * @param string $action_id - the identifier of the action to perform.
     * @param Map $arguments - a set of arguments the action will required in order to execute.
     * @return mixed
     */
    abstract public function perform(string $action_id, Map $arguments);

    /**
     * @return array - an array of action ids.
     */
    abstract public function performsActions();
}
