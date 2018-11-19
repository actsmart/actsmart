<?php

namespace actsmart\actsmart\Utils\Qna;


class QnAResponse
{

    const NO_MATCH = 'No good match found in KB.';

    private $matchFound = true;

    /* @var array $question - the questions associated with the answer */
    private $questions;

    /* @var float $score - the confidence score assigned to the answer. */
    private $score;

    /* @var string $source - the source of the answer. */
    private $source;

    /* @var array $metadata - metadata associated with the answer. */
    private $metadata;

    public function __construct($response)
    {
        foreach ($response->answers as $answer) {

            if ($answer->answer == self::NO_MATCH) {
                $this->matchFound = false;
                continue;
            }

            $this->questions = $answer->questions;
            $this->score = (float) $answer->score;
            $this->source = $answer->source;

            foreach ($answer->metadata as $metadata) {
                $this->metadata[$metadata->name] = $metadata->value;
            }
        }
    }

    /**
     * @return bool
     */
    public function isMatchFound(): bool
    {
        return $this->matchFound;
    }

    /**
     * @return array
     */
    public function getQuestions(): array
    {
        return $this->questions;
    }

    /**
     * @return float
     */
    public function getScore(): float
    {
        return $this->score;
    }

    /**
     * @return string
     */
    public function getSource(): string
    {
        return $this->source;
    }

    /**
     * @return array
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }

    /**
     * @param $key
     * @return string|null
     */
    public function getValueForKey($key): string
    {
        return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
    }
}