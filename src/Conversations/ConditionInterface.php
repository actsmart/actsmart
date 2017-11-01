<?php

namespace actsmart\actsmart\Conversations;

interface ConditionInterface
{
    public function check($e);

    public function getLabel();
}