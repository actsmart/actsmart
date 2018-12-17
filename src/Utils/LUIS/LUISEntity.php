<?php

namespace actsmart\actsmart\Utils\LUIS;

class LUISEntity
{
    /* @var string $type - the type of entity. */
    private $type;

    /* @var string $entityString - the exact match from the query. */
    private $entityString;

    /* @var int $startIndex - where the entity begins in the phrase. */
    private $startIndex;

    /* @var int $endIndex - where the entity ends in the phrase. */
    private $endIndex;

    /* @var array $resolutionValues - if a list type entity this provides all the resolution values. */
    private $resolutionValues;

    /* @var float $score - in case of a simple entity the confidence score for the match. */
    private $score;

    public function __construct($entity)
    {
        $this->type = $entity->type;

        $this->entityString = $entity->entity;

        if (isset($entity->startIndex)) {
            $this->startIndex = $entity->startIndex;
            $this->endIndex = $entity->endIndex;
        }

        if (isset($entity->resolution)) {
            foreach($entity->resolution->values as $value) {
                $this->resolutionValues[] = $value;
            }
        }

        if (isset($entity->score)) {
            $this->score = (int) $entity->score;
        }
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getEntityString()
    {
        return $this->entityString;
    }

    /**
     * @return mixed
     */
    public function getStartIndex()
    {
        return $this->startIndex;
    }

    /**
     * @return mixed
     */
    public function getEndIndex()
    {
        return $this->endIndex;
    }

    /**
     * @return mixed
     */
    public function getResolutionValues()
    {
        return $this->resolutionValues;
    }

    /**
     * @return mixed
     */
    public function getScore()
    {
        return $this->score;
    }
}