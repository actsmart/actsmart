<?php

namespace actsmart\actsmart\Interpreters\KnowledgeGraph;

use \Ds\Map;

class KnowledgeGraphNode
{
    /* @var string */
    protected $name;

    /* @var string */
    protected $type;

    /* @var float */
    protected $weight;

    /* @var \Ds\Map */
    protected $keywords;

    public function __construct(string $name, string $type, float $weight, Map $keywords)
    {
        $this->name = $name;
        $this->type = $type;
        $this->weight = $weight;
        $this->keywords = $keywords;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * @param float $weight
     */
    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }

    /**
     * @return Map
     */
    public function getKeywords(): Map
    {
        return $this->keywords;
    }

    /**
     * @param Map $keywords
     */
    public function setKeywords(Map $keywords): void
    {
        $this->keywords = $keywords;
    }


}