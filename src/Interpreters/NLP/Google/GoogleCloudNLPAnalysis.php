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
        return $this->annotation->tokensByTag('NOUN');
    }

    /**
     * @inheritdoc
     */
    public function getVerbs()
    {
        return $this->annotation->tokensByTag('VERB');
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
}