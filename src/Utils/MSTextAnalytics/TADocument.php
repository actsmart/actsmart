<?php

namespace actsmart\actsmart\Utils\MSTextAnalytics;

/**
 * Class TADocument
 *
 * A TADocument currently only has language analysis associated with it but as per the
 * documentation it can have multiple types of analysis associated with it.
 * https://westus.dev.cognitive.microsoft.com/docs/services/TextAnalytics.V2.0/operations/56f30ceeeda5650db055a3c7
 *
 * @package actsmart\actsmart\Utils\MSTextAnalytics
 */
class TADocument
{
    private $id;

    /* @var \actsmart\actsmart\Utils\MSTextAnalytics\TADetectedLanguage $languageAnalysis */
    private $languageAnalysis;

    public function __construct($id)
    {
        $this->id  = $id;
    }

    public function addLanguageAnalysis($name, $iso6391name, $score)
    {
        $this->languageAnalysis = new TADetectedLanguage($name, $iso6391name, $score);
    }

    /**
     * @return TADetectedLanguage
     */
    public function getLanguageAnalysis(): TADetectedLanguage
    {
        return $this->languageAnalysis;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}