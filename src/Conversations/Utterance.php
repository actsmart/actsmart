<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Interpreters\Intent;
use Fhaculty\Graph\Edge\Directed as EdgeDirected;
use Fhaculty\Graph\Vertex;

class Utterance extends EdgeDirected
{
    private $message;

    private $sequence;

    private $intent;

    public function __construct(Vertex $from, Vertex $to, $sequence)
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

    public function changesScene(){
        if ($this->getVertexStart()->getSceneId() != $this->getVertexEnd()->getSceneId()) return true;

        return false;
    }

    public function intentMatches(Intent $intent)
    {
        if ($intent->getLabel() == $this->intent->getLabel()) return true;
    }
}

