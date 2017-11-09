<?php
namespace actsmart\actsmart\Sensors;

use Symfony\Component\EventDispatcher\GenericEvent;

class SensorEvent extends GenericEvent
{
    protected $event_key;

    public function getKey()
    {
        return $this->event_key;
    }
}