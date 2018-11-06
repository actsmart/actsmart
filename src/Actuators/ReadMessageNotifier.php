<?php

namespace actsmart\actsmart\Actuators;

use actsmart\actsmart\Sensors\ReadMessageEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;


abstract class ReadMessageNotifier implements ComponentInterface, LoggerAwareInterface, ActuatorInterface, ListenerInterface
{
    use LoggerAwareTrait, ComponentTrait, ListenerTrait;

    const KEY           = 'actuator.readmessagenotifier';
    const WEBCHAT_READ_RECEIPT  = 'action.webchat.readreceipt';

    /**
     * @param string $action
     * @param Map $arguments
     * @return mixed
     */
    public function perform(string $action, $arguments)
    {

    }


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
