<?php

namespace actsmart\actsmart\Utils\LUIS;

class LUISResponse
{
    /* @var string $query - the text sent to LUIS for intent analysis */
    private $query;

    /* @var \actsmart\actsmart\Utils\LUIS\LUISIntent $topScoringIntent - the response from
     * LUIS regarding the top scoring intent.
     **/
    private $topScoringIntent;

    /* @var array $entities - any entities identified in the intent */
    private $entities = [];

    public function __construct($response)
    {
        $this->query = isset($response->query) ? $response->query : null;

        $this->topScoringIntent = new LUISIntent($response->topScoringIntent);

        if (isset($response->entities)) {
            $this->createEntities($response->entities);
        }
    }

    /**
     * Extract entities and create LUISEntity objects.
     *
     * @param $entities
     */
    private function createEntities($entities)
    {
        foreach ($entities as $entity) {
            $this->entities[] = new LUISEntity($entity);
        }
    }

    /**
     * @return string
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * @return LUISIntent
     */
    public function getTopScoringIntent(): LUISIntent
    {
        return $this->topScoringIntent;
    }

    /**
     * @return array
     */
    public function getEntities(): array
    {
        return $this->entities;
    }
}