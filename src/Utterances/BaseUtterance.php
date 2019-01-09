<?php

namespace actsmart\actsmart\Utterances;

abstract class BaseUtterance
{
    protected $map;

    public function __construct()
    {
        $this->map = new \DS\Map();
    }

    public function get($key, $default = null)
    {
        return $this->map->get($key, $default);
    }

    public function put($key, $value)
    {
        return $this->map->put($key, $value);
    }
}
