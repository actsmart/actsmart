<?php

namespace actsmart\actsmart\Interpreters\Intent;

use Ds\Map;

interface IntentInterpreter
{
    public function interpretUtterance(Map $utterance): Intent;
}
