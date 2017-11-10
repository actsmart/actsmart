<?php

namespace actsmart\actsmart\Actions;

use actsmart\actsmart\Sensors\SensorEvent;

interface ActionInterface
{
    public function perform(SensorEvent $e);

    public function notify(ActionEvent $a);

    public function getActionName();
}
