<?php

namespace actsmart\actsmart\Utils\MSTextAnalytics;

class TADetectedLanguage
{
    private $name;

    private $iso6391Name;

    private $score;

    public function __construct(string $name, string $iso6391Name, float $score)
    {
        $this->name = $name;
        $this->iso6391Name = $iso6391Name;
        $this->score = $score;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getIso6391Name()
    {
        return $this->iso6391Name;
    }

    /**
     * @return float
     */
    public function getScore()
    {
        return $this->score;
    }
}