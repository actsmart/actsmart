<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 13:10
 */

namespace actsmart\actsmart\Controllers\Active;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\SensorEvent;



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

    public function request(SensorInterface $s = null)
    {

    }

    public function addActuator(ActuatorInterface $actuator)
    {
        $this->actuators[$actuator->getKey()] = $actuator;
    }

    public function addSensor(SensorInterface $sensor)
    {
        $this->sensors[$sensor->getKey()] = $sensor;
    }
}