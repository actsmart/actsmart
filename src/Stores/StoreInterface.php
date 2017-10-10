<?php


namespace actsmart\actsmart\Stores;

interface StoreInterface
{
    public function store($e);

    public function reply();
}