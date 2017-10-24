<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;
use \Fhaculty\Graph\Set\Edges;


class Scene extends Vertex
{
    /* @todo Added as a reminder that Scenes can have pre and post conditions */
    private $preconditions = [];

    private $postconditions = [];

    private $scene_id;

    public function __construct(Graph $graph, $scene_id)
    {
        parent::__construct($graph, $scene_id);

        $this->scene_id = $scene_id;
    }

    public function getSceneId()
    {
        return $this->scene_id;
    }

    /**
     * @return \Fhaculty\Graph\Set\Vertices - of type Participant
     */
    public function getParticipants()
    {
        // Participants are Vertices of Type Participant
        return $this->getVerticesEdgeTo()->getVerticesMatch( function($participant) {
            if ($participant instanceof Participant) return $participant;
        });
    }

    public function getParticipant($participant_id)
    {
        $participants = $this->getParticipants();
        return $participants->getVertexId($this->scene_id . '/' . $participant_id);
    }

    public function getAllUtterances()
    {
        $utterances = [];

        foreach($this->getParticipants() as $participant)
        {
            foreach($participant->getUtterances() as $utterance)
            {
                $utterances[$utterance->getSequence()] = $utterance;
            }
        }
        ksort($utterances);
        return new Edges($utterances);
    }

    public function getExitUtterances()
    {
        $exit_utterances = [];
        foreach($this->getAllUtterances() as $utterance)
        {
            if ($utterance->changesScene()) $exit_utterances[] = $utterance;
        }
        return new Edges($exit_utterances);
    }

    public function getInternalUtterances()
    {
        $internal_utterances = [];
        foreach($this->getAllUtterances() as $utterance)
        {
            if (!$utterance->changesScene()) $internal_utterances[] = $utterance;
        }
        return new Edges($internal_utterances);
    }

    public function getInitialUtterance()
    {
        return $this->getAllUtterances()->getEdgeFirst();
    }

}