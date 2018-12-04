<?php

namespace actsmart\actsmart\Utils\MSTextAnalytics;

/**
 * Class TAResponse
 * @package actsmart\actsmart\Utils\MSTextAnalytics
 */
class TAResponse
{
    private $documents;

    public function __construct($response)
    {
        $this->documents = [];

        foreach ($response->documents as $document) {
            $taDocument = new TADocument($document->id);
            foreach ($document->detectedLanguages as $language) {
                $taDocument->addLanguageAnalysis($language->name,
                    $language->iso6391Name,
                    (float) $language->score);
            }
            $this->documents[$taDocument->getId()] = $taDocument;
        }
    }

    /**
     * @param $is
     * @return bool|mixed
     */
    public function getDocumentWithId($id) {
        if (isset($this->documents[$id])) {
            return $this->documents[$id];
        }

        return false;
    }

}