<?php

namespace actsmart\actsmart\Interpreters\NLP;

/**
 * An NLP Interpreter has an analyse method that takes a string utterance as an input and returns an
 * @see NLPAnalysis object
 *
 * Each NLP service should have it's own implementation of an interpreter and an analysis object
 */
interface NLPInterpreter
{
    /**
     * Takes an input string, passes it through NLP analysis and returns an @see NLPAnalysis object
     *
     * @param string $utterance
     * @return NLPAnalysis
     */
    public function analyse(string $utterance): NLPAnalysis;
}
