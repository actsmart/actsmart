<?php

namespace actsmart\actsmart\Utils;

use actsmart\actsmart\Agent;

/**
 * Interface ComponentInterface
 * @package actsmart\actsmart\Utils
 *
 * A component in actSmart is one of a Sensor, Actuator, Controller, Store or Interpreter. The basic components that
 * make up an agent and are controlled through the Agent kernel.
 */
interface ComponentInterface
{
    public function getKey();

    public function setAgent(Agent $agent);
}