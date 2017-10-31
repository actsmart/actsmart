<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Actions\ActionEvent;

class ContextStore
{

    private $context_info = [];

    public function __construct()
    {
        //
    }

    public function store(ActionEvent $a)
    {
        $this->context_info[$a->getSubject()] = $a->getActionStatus();
    }

    public function retrieve($label)
    {
        return $this->context_info[$label];
    }

}