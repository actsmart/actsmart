<?php
namespace actsmart\actsmart\Actions;

use Symfony\Component\EventDispatcher\GenericEvent;

class ActionEvent extends GenericEvent
{
    private $type;

    private $action_status;

    public function __construct($type, $action_status)
    {
        $this->action_status = $action_status;
        parent::__construct($type, (array) $action_status);
    }


    public function getActionStatus()
    {
        return $this->action_status;
    }

    public function getType()
    {
        return $this->type;
    }
}
