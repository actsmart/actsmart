<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;
use \Fhaculty\Graph\Set\Edges;
use actsmart\actsmart\Interpreters\InterpreterInterface;


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
        return $this->getVerticesEdgeTo()->getVerticesMatch( function($participant) {
            if ($participant instanceof Participant) return $participant;
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
     * @return Edges
     */
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

    /**
     * Get utterances that lead on to other scenes.
     *
     * @return Edges
     */
    public function getExitUtterances()
    {
        $exit_utterances = [];
        foreach($this->getAllUtterances() as $utterance)
        {
            if ($utterance->changesScene()) $exit_utterances[] = $utterance;
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
        foreach($this->getAllUtterances() as $utterance)
        {
            if (!$utterance->changesScene()) $internal_utterances[] = $utterance;
        }
        return new Edges($internal_utterances);
    }

    /**
     * Get the opening utterance to this scene.
     *
     * @return \Fhaculty\Graph\Edge\Base
     */
    public function getInitialUtterance()
    {
        return $this->getAllUtterances()->getEdgeFirst();
    }

    /**
     * @param $sequence
     * @return mixed
     */
    public function getUtteranceWithSequence($sequence){
        $utterances = $this->getAllUtterances();
        foreach ($utterances as $utterance){
            if ($utterance->getSequence() == $sequence) return $utterance;
        }
        return false;
    }

    /**
     * @param $current_sequence
     * @return array
     */
    public function getPossibleFollowUps($current_sequence)
    {
        $current_utterance = $this->getUtteranceWithSequence($current_sequence);
        $current_sender = $current_utterance->getVertexStart()->getParticipantId();

        $possible_followups = [];
        foreach($this->getAllUtterances() as $utterance)
        {
            if ($utterance->getSequence() <= $current_sequence) {
                continue;
            }

            if (($utterance->getVertexStart()->getParticipantId() != $current_sender) &&
                $utterance->getVertexEnd()->getParticipantId() == $current_sender) {
                $possible_followups[$utterance->getSequence()] = $utterance;
            }
        }

        return $possible_followups;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addPrecondition(Condition $condition)
    {
        $this->preconditions[$condition->getLabel()] = $condition;
        return $this;
    }

    /**
     * @param $e
     * @return bool
     */
    public function checkPreconditions($e)
    {
        foreach ($this->preconditions as $precondition) {
            if (!$precondition->check($e)) return false;
        }
        return true;
    }

}