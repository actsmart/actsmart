<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Interpreters\Intent\Intent;
use Ds\Map;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Vertex;

class Utterance extends EdgeDirected
{
    private $message;

    private $sequence;

    /** @var Intent */
    private $intent;

    private $completes;

    private $action;

    private $informationRequest;

    private $preconditions = [];

    private $intent_interpreter;

    public function __construct(Vertex $from, Vertex $to, $sequence, $completes = false)
    {
        parent::__construct($from, $to);
        $this->sequence = $sequence;
    }

    public function setMessage(Message $message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function setIntent(Intent $intent)
    {
        $this->intent = $intent;
        return $this;
    }

    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @return mixed
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @return Utterance
     */
    public function setSequence($sequence)
    {
        $this->sequence = $sequence;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isCompleting()
    {
        return $this->completes;
    }

    /**
     * @param mixed $completes
     * @return Utterance
     */
    public function setCompletes($completes)
    {
        $this->completes = $completes;
        return $this;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param  string - $action
     * @return Utterance
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getInformationRequest()
    {
        return $this->informationRequest;
    }

    /**
     * @param mixed $informationRequest
     * @return Utterance
     */
    public function setInformationRequest($informationRequest)
    {
        $this->informationRequest = $informationRequest;
        return $this;
    }

    /**
     * @return array
     */
    public function getPreconditions()
    {
        return $this->preconditions;
    }

    /**
     * @param string $precondition
     * @return Utterance
     */
    public function addPrecondition($precondition)
    {
        $this->preconditions[] = $precondition;
        return $this;
    }

    public function changesScene()
    {
        if ($this->getStartScene() != $this->getEndScene()) {
            return true;
        }
        return false;
    }

    public function getStartScene()
    {
        return $this->getVertexStart()->getSceneId();
    }

    public function getEndScene()
    {
        return $this->getVertexEnd()->getSceneId();
    }

    public function getSender()
    {
        return $this->getVertexStart()->getParticipantId();
    }

    public function getReceiver()
    {
        return $this->getVertexEnd()->getParticipantId();
    }

    public function intentMatches(Intent $intent)
    {
        if (($this->intent->getLabel() == $intent->getLabel()) &&
            ($this->intent->getConfidence() <= $intent->getConfidence())) {
            return true;
        }
        return false;
    }

    public function setInterpreter($intent_interpreter)
    {
        $this->intent_interpreter = $intent_interpreter;
    }

    public function getIntentInterpreter()
    {
        return $this->intent_interpreter;
    }

    public function hasIntentInterpreter()
    {
        return isset($this->intent_interpreter);
    }

    public function interpretIntent(Map $utterance)
    {
        return $this->intent_interpreter->interpretIntent($utterance);
    }
}
