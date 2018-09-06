<?php

namespace actsmart\actsmart\Interpreters\Intent;

use Ds\Map;

class Intent
{
    /* @var string */
    private $label;

    /* @var float */
    private $confidence;

    /* @var \Ds\Map  */
    private $source_utterance;

    public function __construct(string $label = '', Map $source_utterance = null, float $confidence = 1)
    {
        $this->label = $label;
        $this->$source_utterance = $source_utterance;
        $this->confidence = $confidence;
    }

    /**
     * @return mixed
     */
    public function getLabel() : string
    {
        return $this->label;
    }

    /**
     * @param mixed $label
     * @return string
     */
    public function setLabel($label)
    {
        $this->label = $label;
        return $this;
    }

    /**
     * @return Map
     */
    public function getSourceUtterance() : Map
    {
        return $this->source_utterance;
    }

    /**
     * @param Map $source_utterance
     * @return Intent
     */
    public function setSourceUtterance(Map $source_utterance)
    {
        $this->source_utterance = $source_utterance;
        return $this;
    }

    /**
     * @return int
     */
    public function getConfidence() : float
    {
        return $this->confidence;
    }

    /**
     * @param int $confidence
     */
    public function setConfidence($confidence)
    {
        $this->confidence = $confidence;
    }
}
