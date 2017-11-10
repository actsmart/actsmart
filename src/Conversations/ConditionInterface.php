<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Sensors\SensorEvent;

interface ConditionInterface
{
    public function check(SensorEvent $e);

    public function getLabel();
}
