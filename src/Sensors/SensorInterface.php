<?php

namespace actsmart\actsmart\Sensors;


interface SensorInterface
{
    /*
     * Receive an input.
     */
    public function receive($message);

    /**
     * Process an input.
     */
    public function process();

    /**
     * Store an input.
     */
    public function store();
}