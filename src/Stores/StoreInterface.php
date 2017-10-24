<?php

namespace actsmart\actsmart\Stores;

interface StoreInterface
{
    public function store($data);

    public function reply();
}