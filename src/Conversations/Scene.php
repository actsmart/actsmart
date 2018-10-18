<?php

namespace actsmart\actsmart\Conversations;

use Fhaculty\Graph\Graph as Graph;
use Fhaculty\Graph\Set\Edges;
use Fhaculty\Graph\Vertex;

class Scene extends Vertex
{
    private $preconditions = [];

    /* @todo Added as a reminder that Scenes can have pre- and post- conditions */
    private $postconditions = [];

    private $scene_id;

    public function __construct(Graph $graph, $scene_id)
    {
        parent::__construct($graph, $scene_id);
        $this->scene_id = $scene_id;
    }

    /**
     * @return int|string
     */
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
        return $this->getVerticesEdgeTo()->getVerticesMatch(function ($participant) {
            if ($participant instanceof Participant) {
                return $participant;
            }
        });
    }

    /**
     * Get a specific participant to this Scene.
     * @param $participant_id
     * @return Vertex
     */
    public function getParticipant($participant_id)
    {
        $participants = $this->getParticipants();
        return $participants->getVertexId($this->scene_id . '/' . $participant_id);
    }

    /**
     * Get all utterances exchanges in this Scene.
     *
     * @return array
     */
    public function getAllUtterancesKeyedBySequence()
    {
        $utterances = [];

        foreach ($this->getParticipants() as $participant) {
            foreach ($participant->getUtterances() as $utterance) {
                $utterances[$utterance->getSequence()] = $utterance;
            }
        }
        ksort($utterances);
        return $utterances;
    }

    public function getAllUtterances()
    {
        return new Edges($this->getAllUtterancesKeyedBySequence());
    }

    /**
     * Get utterances that lead on to other scenes.
     *
     * @return Edges
     */
    public function getExitUtterances()
    {
        $exit_utterances = [];
        foreach ($this->getAllUtterances() as $utterance) {
            if ($utterance->changesScene()) {
                $exit_utterances[] = $utterance;
            }
        }
        return new Edges($exit_utterances);
    }

    /**
     * Get only utterances that are internal to the scene.
     *
     * @return Edges
     */
    public function getInternalUtterances()
    {
        $internal_utterances = [];
        foreach ($this->getAllUtterances() as $utterance) {
            if (!$utterance->changesScene()) {
                $internal_utterances[] = $utterance;
            }
        }
        return new Edges($internal_utterances);
    }

    /**
     * Get the opening utterance to this scene.
     *
     * @return Utterance
     */
    public function getInitialUtterance()
    {
        return $this->getAllUtterances()->getEdgeFirst();
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addPrecondition($condition)
    {
        $this->preconditions[] = $condition;
        return $this;
    }

    /**
     * @return array
     */
    public function getPreconditions()
    {
        return $this->preconditions;
    }
}
