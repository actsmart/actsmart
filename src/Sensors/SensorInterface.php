<?php

namespace actsmart\actsmart\Sensors;

/**
 * Sensors receive input from outside sources, process it and then notify any listeners (typically Stores) of that
 * input.
 *
 * Interface SensorInterface
 * @package actsmart\actsmart\Sensors
 */
interface SensorInterface
{
    /**
     * Receive an input.
     */
    public function receive($message);

    /**
     * Process the input.
     */
    public function process($message);
}
