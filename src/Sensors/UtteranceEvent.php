<?php

namespace actsmart\actsmart\Sensors;

use Ds\Map;

/**
 * Interface UtteranceEvent
 * An UtteranceEvent is an event that signifies that a statement was made in a dialog.
 */
interface UtteranceEvent
{
    public function getUtterance() : Map;
}