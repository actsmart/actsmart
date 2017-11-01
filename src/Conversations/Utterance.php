<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Interpreters\Intent;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Vertex;
use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Actions\ActionInterface;
use actsmart\actsmart\Conversations\ConditionInterface;
use actsmart\actsmart\Sensors\SensorEvent;

class Utterance extends EdgeDirected
{
    private $message;

    private $sequence;

    private $intent;

    private $completes;

    private $action;

    private $precondition;

    /* @var actsmart\actsmart\Interpreters\InterpreterInterface $interpreter */
    private $interpreter = null;

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
     * @param mixed $order
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
     */
    public function setCompletes($completes)
    {
        $this->completes = $completes;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param ActionInterface $action
     * @return Utterance
     */
    public function setAction(ActionInterface $action)
    {
        $this->action = $action;
        return $this;
    }

    public function performAction(SensorEvent $event)
    {
        if (isset($this->action)) $this->action->perform($event);
    }

    /**
     * @return mixed
     */
    public function getPrecondition()
    {
        return $this->precondition;
    }

    /**
     * @param ConditionInterface $precondition
     * @return Utterance
     */
    public function setPrecondition(ConditionInterface $precondition)
    {
        $this->precondition = $precondition;
        return $this;
    }

    public function checkPrecondition(SensorEvent $e)
    {
       if (isset($this->precondition)) {
           return $this->precondition->check($e);
       }

       return true;
    }

    public function changesScene()
    {
        if ($this->getStartScene() != $this->getEndScene()) return true;
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
            ($this->intent->getConfidence() <= $intent->getConfidence())) return true;
        return false;
    }

    public function setInterpreter(InterpreterInterface $interpreter)
    {
        $this->interpreter = $interpreter;
    }

    public function hasInterpreter()
    {
        return isset($this->interpreter);
    }

    public function interpret($e)
    {
        return $this->interpreter->interpret($e);
    }

}

