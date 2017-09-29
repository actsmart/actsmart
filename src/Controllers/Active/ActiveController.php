<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 13:10
 */

namespace actsmart\actsmart\Controllers\Active;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Actuators\ActuatorInterface;


class ActiveController implements ControllerInterface
{

    /** @var  Agent */
    protected $agent;

    protected $actuators = [];

    public function __construct(Agent $agent)
    {
        $this->agent = $agent;
    }


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