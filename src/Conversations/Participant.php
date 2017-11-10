<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;

class Participant extends Vertex
{
    private $participant_id;

    private $scene_id;

    public function __construct(Graph $graph, $scene_id, $participant_id)
    {
        // The Vertex id is a merge of scene and participant id as vertices need unique ids
        parent::__construct($graph, $scene_id . '/' . $participant_id);
        $this->participant_id = $participant_id;
        $this->scene_id = $scene_id;
    }

    public function getParticipantId()
    {
        return $this->participant_id;
    }

    public function getSceneId()
    {
        return $this->scene_id;
    }

    public function talksTo(Participant $receiver, $sequence)
    {
        return new Utterance($this, $receiver, $sequence);
    }

    public function getUtterances()
    {
        return $this->getEdgesOut();
    }

    public function getExitUtterances()
    {
        $exit_utterances = [];
        foreach ($this->getUtterances() as $utterance) {
            if ($utterance->changesScene()) {
                $exit_utterances[] = $utterance;
            }
        }
        return new Edges($exit_utterances);
    }

    public function getExitUtteranceToScene($scene_id)
    {
    }
}
