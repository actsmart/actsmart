<?php

namespace actsmart\actsmart\Conversations;

use Ds\Map;

interface ConditionInterface
{
    public function check(Map $utterance);
}
