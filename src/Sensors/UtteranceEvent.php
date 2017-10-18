<?php

namespace actsmart\actsmart\Sensors;

/**
 * Interface Utterance
 * An utterance is an event that represents a statement that was made in a dialog.
 *
 * @package actsmart\actsmart\Sensors
 */
interface UtteranceEvent
{
    public function getUtterance();
}