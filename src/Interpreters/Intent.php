<?php

namespace actsmart\actsmart\Interpreters;


class Intent
{
    private $label;

    private $confidence;

    private $source_event;

    public function __construct($label = null, $source_event = null, $confidence = 1)
    {
        $this->label = $label;
        $this->source_event = $source_event;
        $this->confidence = $confidence;
    }

    /**
     * @return mixed
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return Intent
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSourceEvent()
    {
        return $this->source_event;
    }

    /**
     * @param mixed $source_event
     * @return Intent
     */
    public function setSourceEvent($source_event)
    {
        $this->source_event = $source_event;
        return $this;
    }


}