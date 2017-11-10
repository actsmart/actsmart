<?php

namespace actsmart\actsmart\Interpreters;

use actsmart\actsmart\Sensors\SensorEvent;

interface InterpreterInterface
{
    public function interpret(SensorEvent $e);
}
