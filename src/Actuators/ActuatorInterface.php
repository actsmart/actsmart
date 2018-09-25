<?php
namespace actsmart\actsmart\Actuators;

use Symfony\Component\EventDispatcher\GenericEvent;
use Ds\Map;

interface ActuatorInterface
{
    /**
     * @param string $action_id - the identifier of the action to perform.
     * @param Map $arguments - a set of arguments the action will required in order to execute.
     * @return mixed
     */
    public function perform(string $action_id, Map $arguments = null);

    /**
     * @return array - an array of action ids.
     */
    public function performsActions();
}
