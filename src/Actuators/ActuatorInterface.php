<?php
namespace actsmart\actsmart\Actuators;

use Symfony\Component\EventDispatcher\GenericEvent;

interface ActuatorInterface
{
    /**
     * @param $action_id - the identified of the action to perform
     * @param $object - an object relevant to the action to be perform.
     * @return mixed
     */
    public function perform($action_id, $object);

    /**
     * @return array - an array of action ids.
     */
    public function performsActions();
}