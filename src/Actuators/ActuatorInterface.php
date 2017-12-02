<?php
namespace actsmart\actsmart\Actuators;

use Symfony\Component\EventDispatcher\GenericEvent;

interface ActuatorInterface
{
    /**
     * @param $action_id - the identifier of the action to perform.
     * @param $arguments - a set of arguments the action will required in order to execute.
     * @return mixed
     */
    public function perform(string $action_id, $arguments = []);

    /**
     * @return array - an array of action ids.
     */
    public function performsActions();
}