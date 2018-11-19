<?php

namespace actsmart\actsmart\Utils\LUIS;


class LUISIntent
{
    /* @var string $label - the identifier for the intent in LUIS. */
    private $label;

    /* @var float $score - the confidence score assigned to the intent. */
    private $score;

    public function __construct($intent)
    {
        $this->label = $intent->intent;
        $this->score = (float) $intent->score;
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
     */
    public function setLabel($label): void
    {
        $this->label = $label;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * @param float $score
     */
    public function setScore($score): void
    {
        $this->score = $score;
    }

}