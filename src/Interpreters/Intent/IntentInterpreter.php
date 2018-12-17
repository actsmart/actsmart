<?php

namespace actsmart\actsmart\Interpreters\Intent;

use actsmart\actsmart\Utils\ComponentInterface;
use Ds\Map;

interface IntentInterpreter extends ComponentInterface
{
    public function interpretUtterance(Map $utterance): Intent;
}
