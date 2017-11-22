<?php
namespace actsmart\actsmart\Interpreters;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class BaseInterpreter implements InterpreterInterface, ComponentInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait;
}
