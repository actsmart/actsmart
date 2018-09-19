<?php

namespace actsmart\actsmart\Interpreters\KnowledgeGraph;

use Ds\Map;

interface KnowledgeGraphObject
{
    public function getNodes() : Map;

    public function getRawResponse() : string;
}