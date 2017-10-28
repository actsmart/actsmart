<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;
use \Fhaculty\Graph\Set\Edges;
use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Interpeters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;


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
     * @return array
     */
    public function getAllUtterancesKeyedBySequence()
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
     * @param $current_sequence
     * @param SensorEvent $e
     * @param null $default_intent
     * @return array
     */
    public function getMatchingUtterances($current_sequence, SensorEvent $e, $default_intent = null)
    {
        // Check each possible followup for a match
        $matching_followups = [];
        foreach ($this->getPossibleFollowUps($current_sequence) as $followup) {
            if ($followup->hasInterpreter()) {
                if ($followup->intentMatches($followup->interpret($e))) {$matching_followups[] = $followup;}
            } else {
                if ($followup->intentMatches($default_intent)) {$matching_followups[] = $followup;}
            }
        }

        return $matching_followups;
    }

    public function getNextUtterance($sequence, SensorEvent $e, Intent $default_intent)
    {
        $matching_utterances = $this->getMatchingUtterances($sequence, $e, $default_intent);

        // We couldn't find any matching intent. Get out.
        if (count($matching_utterances) == 0) return false;

        // At this point we definitely have a matching intent so let us post the corresponsing message.
        // Keeping it simple - just the first matching utterance. We are keeping it simple - just the first
        // matching utterance.
        $current_utterance = $matching_utterances[0];
        $next_utterance = null;

        // There are two possibilities.
        // 1. We are in the same scene so we get the next message in that scene. Let's check.
        if (!$current_utterance->changesScene()) {
            $next_utterance = $this->getNextSequentialUtterance($sequence);
        } else {
            // We *are* changing scenes! Set the current scene as the new scene.

            // Get the new scene and the first utterance that is a reply to this one.

            //$ci->setCurrentSceneId($current_utterance->getEndScene());

            // Given the new scene the next message is going to be the first message of the scene that
            // has a sequence higher than the current message and replies to the current message sender
            // @todo - we have to improve get nextUtterance to be more generic than just the message with the next
            // sequence id.
            //$ci->getNextUtterance();
        }
    }

    public function getNextUtteranceInSequence($sequence)
    {

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