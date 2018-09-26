<?php
namespace actsmart\actsmart\Interpreters\KnowledgeGraph\DGraph;

use actsmart\actsmart\Interpreters\KnowledgeGraph\KnowledgeGraphInterpreter;
use actsmart\actsmart\Interpreters\KnowledgeGraph\KnowledgeGraphObject;
use actsmart\actsmart\Interpreters\NLP\NLPAnalysis;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\DGraph\DGraphClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;


abstract class DGraphInterpreter implements KnowledgeGraphInterpreter, ComponentInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait;

    protected $dGraphClient;

    public function __construct(DGraphClient $dGraphclient) {
        $this->dGraphClient = $dGraphclient;
    }

    public abstract function analyse(NLPAnalysis $analysis);

}