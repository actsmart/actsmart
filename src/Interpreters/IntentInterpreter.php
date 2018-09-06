<?php

namespace actsmart\actsmart\Interpreters;

use Ds\Map;

interface IntentInterpreter
{
    public function interpretUtterance(Map $utterance): Intent;
}
