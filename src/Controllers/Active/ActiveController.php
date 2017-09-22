<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 13:10
 */

namespace actsmart\actsmart\Controllers\Active;

use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Actuators\ActuatorInterface;


class ActiveController implements ControllerInterface
{

    protected $actuators = [];

    public function act(ActuatorInterface $a)
    {

    }

    public function execute(SensorEvent $e = null)
    {

    }

    public function addActuator(ActuatorInterface $actuator)
    {
        $this->actuators[$actuator->getKey()] = $actuator;
    }
}