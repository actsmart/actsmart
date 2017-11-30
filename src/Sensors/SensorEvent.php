<?php
namespace actsmart\actsmart\Sensors;

use Symfony\Component\EventDispatcher\GenericEvent;

class SensorEvent extends GenericEvent
{
    //@todo this is not required - doublecheck.
    protected $event_key;

    //@todo this is not required - doublecheck.
    public function getKey()
    {
        return $this->event_key;
    }
}
