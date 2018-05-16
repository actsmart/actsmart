<?php

namespace actsmart\actsmart\Utils;

/**
 * Trait ListenerTrait
 * @package actsmart\actsmart\Utils
 *
 * Implementation of base Listener functionality
 */
trait ListenerTrait
{

    protected $priority;

    /**
     * @param $p
     */
    public function setPriority($p)
    {
        $this->priority = $p;
    }

    /**
     * Returns priority or null if priority is not set.
     *
     * @return int
     */
    public function getPriority()
    {
        return $this->priority ?? 0;
    }
}
