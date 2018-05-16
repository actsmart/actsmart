<?php
namespace actsmart\actsmart\Actuators;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Some actuators may raise ActionEvents that will carry information about what the actuator just did to store within
 * the application context.
 */
class ActionEvent extends GenericEvent
{
    protected $event_key;

    public function __construct($subject = null, array $arguments = array(), $event_key = 'event.action.generic')
    {
        parent::__construct($subject, $arguments);
        $this->event_key = $event_key;
    }

    public function getKey()
    {
        return $this->event_key;
    }
}
