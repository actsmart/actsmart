<?php
namespace actsmart\actsmart\Interpreters;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;

abstract class BaseInterpreter implements InterpreterInterface, ComponentInterface
{
    use ComponentTrait;

}
