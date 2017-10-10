<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Controllers\ControllerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;


class Agent
{
    /** @var  SensorInterface */
    protected $sensors;

    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var  Response */
    protected $http_response = null;

    public function __construct(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    public function addSensor(SensorInterface $sensor)
    {
        $this->sensors[$sensor->getKey()] = $sensor;
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

    public function bindSensorToController(SensorInterface $sensor, ControllerInterface $controller)
    {
        $this->dispatcher->addListener($sensor->getEventName(), array($controller, 'execute'));
    }


    public function sensorReceive($sensor_key, $message)
    {
        return $this->sensors[$sensor_key]->receive($message);
    }

    public function setHttpReaction(Response $response)
    {
        $this->http_response = $response;
    }

    public function httpReact()
    {
        if ($this->http_response == null) {
            return new Response('', Response::HTTP_OK, ['content-type' => 'text/html']);
        }
        return $this->http_response;
    }
}