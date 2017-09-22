<?php

namespace actsmart\actsmart\Actuators;


interface ActuatorInterface
{
    public function act($message);

    public function getKey();

}