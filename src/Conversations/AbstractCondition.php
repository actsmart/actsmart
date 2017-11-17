<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;

abstract class AbstractCondition implements ComponentInterface, ConditionInterface
{
    use ComponentTrait;
}