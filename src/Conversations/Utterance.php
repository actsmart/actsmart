<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Edge\Directed as EdgeDirected;
use \Fhaculty\Graph\Vertex;

class Utterance extends EdgeDirected
{
    private $message;

    private $sequence;

    public function __construct(Vertex $from, Vertex $to, $sequence)
    {
        parent::__construct($from, $to);
        $this->sequence = $sequence;
    }

    public function addMessage(Message $message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
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
}

