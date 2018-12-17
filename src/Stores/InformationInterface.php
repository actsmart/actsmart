<?php

namespace actsmart\actsmart\Stores;

/**
 * Information interfaces tags objects as information objects exchanged withing the agent.
 *
 * Interface InformationInterface
 * @package actsmart\actsmart\Stores
 */
interface InformationInterface
{
    public function getType();

    public function getId();

    public function getValue();

}