<?php

namespace actsmart\actsmart\Controllers\Reactive;

use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Sensors\SensorEvent;


class ReactiveController implements ControllerInterface
{
    public function execute(SensorEvent $e = null)
    {
    }
}