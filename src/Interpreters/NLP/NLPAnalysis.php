<?php

namespace actsmart\actsmart\Interpreters\NLP;

/**
 * Interface describing what is returned from an @see NLPInterpreter.
 */
interface NLPAnalysis
{
    /**
     * Returns the full input string sent to the interpreter
     *
     * @return string
     */
    public function getInput();

    /**
     * Returns an array of @see Nouns
     *
     * @return mixed
     */
    public function getNouns();

    public function getVerbs();

    public function getEntities();

    public function getSentences();

    public function getSentence(int $sentenceNumber);

    public function getLanguage();
}
