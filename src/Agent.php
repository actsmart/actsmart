<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Sensors\SensorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;


class Agent
{
    /** @var  SensorInterface */
    protected $sensors;

    /** @var EventDispatcher */
    protected $dispatcher;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function addSensor(SensorInterface $sensor)
    {
        $this->sensors[$sensor->getName()] = $sensor;
    }

    /**
     * Makes an infostore a listener to sensor events.
     *
     * @param $sensor
     * @param $infostore
     */
    public function bindSensorToStore($sensor, $store)
    {
        $this->dispatcher->addListener($sensor->getEventName(), array($store, 'store'));
    }


    public function sensorReceive($sensor_name, $message)
    {
        return $this->sensors[$sensor_name]->receive($message);
    }
}