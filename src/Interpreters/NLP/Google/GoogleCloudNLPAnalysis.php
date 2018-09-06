<?php

namespace actsmart\actsmart\Interpreters\NLP\Google;

use actsmart\actsmart\Interpreters\NLP\NLPAnalysis;
use Google\Cloud\Language\Annotation;

/**
 * The Google specific NLP Analysis object. Basically acts as a wrapper to the @see Annotation object returned
 * from the Google Cloud SDK
 */
class GoogleCloudNLPAnalysis implements NLPAnalysis
{
    /**
     * @var Annotation The Google annotation object
     */
    private $annotation;

    /**
     * @var string The original input string that was interpreted
     */
    private $input;

    public function __construct(Annotation $annotation, string $input)
    {
        $this->annotation = $annotation;
        $this->input = $input;
    }

    /**
     * @inheritdoc
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * @inheritdoc
     */
    public function getNouns()
    {
        $nouns = [];
        foreach ($this->annotation->tokensByTag('NOUN') as $noun) {
            $content = $noun['text']['content'];
            if ($this->isValid($content)) {
                $nouns[] = $content;
            }
        }

        return $nouns;
    }

    /**
     * @inheritdoc
     */
    public function getVerbs()
    {
        $verbs = [];
        foreach ($this->annotation->tokensByTag('VERB') as $verb) {
            $content = $verb['text']['content'];

            if ($this->isValid($content)) {
                $verbs[] = $content;
            }
        }

        return $verbs;
    }
    
    public function getEntities()
    {
        $entities = [];

        foreach ($this->annotation->entities() as $entity) {
            $entities[] = $entity['name'];
        }

        return $entities;
    }

    /**
     * @inheritdoc
     */
    public function getSentences()
    {
        return $this->annotation->sentences();
    }

    /**
     * @inheritdoc
     */
    public function getSentence(int $sentenceNumber)
    {
        return isset($this->annotation->sentences()[$sentenceNumber]) ? $this->annotation->sentences()[$sentenceNumber] : null;
    }

    /**
     * @inheritdoc
     */
    public function getLanguage()
    {
        return $this->annotation->language();
    }

    /**
     * Because Google is returning double quotes as a noun. Checks if the word is valid
     *
     * TODO This should be expanded on as we notice an more irregularities
     *
     * @param $word
     */
    private function isValid($word)
    {
        return $word !== '"';
    }
}