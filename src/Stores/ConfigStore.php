<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;

/**
 * Class ConfigStore
 * @package actsmart\actsmart\Stores
 *
 * Stores any configuration that various components may require.
 */
class ConfigStore implements ComponentInterface, StoreInterface
{
    use ComponentTrait;

    private $configuration = [];

    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $this->configuration[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->configuration[$key];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'store.config';
    }
}