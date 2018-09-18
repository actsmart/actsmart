<?php

namespace actsmart\actsmart\Interpreters\NLP\Google;

use actsmart\actsmart\Interpreters\NLP\NLPAnalysis;
use actsmart\actsmart\Interpreters\NLP\NLPInterpreter;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use Google\Cloud\Language\LanguageClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

class GoogleCloudInterpreter implements NLPInterpreter, ComponentInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait;

    private $features = ['syntax', 'entities'];

    /** @var LanguageClient */
    private $client;

    /**
     * GoogleCloudInterpreter constructor.
     * @param $credentialsFilePath string The fully qualified path to the google cloud credentials json file
     */
    public function __construct($credentialsFilePath)
    {
        $language = new LanguageClient([
            'keyFilePath' => $credentialsFilePath
        ]);
        
        $this->client = $language;
    }

    /**
     * Takes an input string, passes it through NLP analysis and returns an @see NLPAnalysis object
     *
     * @param string $utterance
     * @return NLPAnalysis
     */
    public function analyse(string $utterance): NLPAnalysis
    {
        $annotation = $this->client->annotateText($utterance, ['features' => $this->features]);

        return new GoogleCloudNLPAnalysis($annotation, $utterance);
    }

    public function getKey() {
        return Literals::GOOGLE_CLOUD_NLP;
    }
}
