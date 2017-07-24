<?php

namespace actsmart\actsmart\Sensors;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

interface SensorInterface
{
    /**
     * Receive an input.
     */
    public function receive(SymfonyRequest $message);

    /**
     * Process an input.
     */
    public function process();

    /**
     * Notify listeners of output
     */
    public function notify();
}