<?php
namespace actsmart\actsmart\Interpreters\Intent;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class BaseIntentInterpreter implements IntentInterpreter, ComponentInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait;
}
