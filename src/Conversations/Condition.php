<?php

namespace actsmart\actsmart\Conversations;

interface Condition
{
    public function check($e);

    public function getLabel();
}