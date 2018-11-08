<?php

namespace actsmart\actsmart\Actuators;

use actsmart\actsmart\Sensors\ReadMessageEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * Base actuator for dealing with @see ReadMessageEvent
 */
abstract class ReadMessageNotifier implements ComponentInterface, LoggerAwareInterface, ActuatorInterface, ListenerInterface
{
    use LoggerAwareTrait, ComponentTrait, ListenerTrait;

    const KEY                   = 'actuator.readmessagenotifier';
    const WEBCHAT_READ_RECEIPT  = 'action.webchat.readreceipt';

    /**
     * @param string $action
     * @param Map $arguments
     * @return mixed
     */
    abstract function perform(string $action, Map $arguments);

    /**
     * @return string
     */
    public function getKey()
    {
        return self::KEY;
    }

    /**
     * @return array
     */
    public function performsActions()
    {
        return [self::WEBCHAT_READ_RECEIPT];
    }

    public function listensForEvents()
    {
        return [ReadMessageEvent::EVENT_NAME];
    }
}
