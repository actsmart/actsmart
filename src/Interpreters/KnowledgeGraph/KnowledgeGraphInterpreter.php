<?php

namespace actsmart\actsmart\Interpreters\KnowledgeGraph;

use actsmart\actsmart\Interpreters\NLP\NLPAnalysis;

interface KnowledgeGraphInterpreter
{
    public function analyse(NLPAnalysis $nlp);
}