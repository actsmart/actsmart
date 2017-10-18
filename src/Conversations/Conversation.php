<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;

class Conversation extends Graph
{
    const UTTERANCE = 'utterance';

    private $scenes = [];

    private $participants = [];

    private $utterance = [];

    public function createScene($scene_id)
    {
        $scene = $this->createVertex($scene_id);
        return $scene;
    }

    public function addParticipantToScene($scene_id, $participant_id)
    {
        // When adding a participant their id is prefixed with the scene_id indicating a
        // specific distinguished stage of the dialog (aka a scene).
        $participant = $this->createVertex($scene_id . '_' . $participant_id);

        // Get the scene and connect the participant to the scene
        $this->getVertex($scene_id)->createEdgeTo($participant);

        return $participant;
    }

    /**
     * An utterance is an edge between two vertices that are participants to a
     * conversation.
     *
     * @param $scene
     * @param $sender
     * @param $receiver
     * @param $utterance
     */
    public function addUtterance($scene_id, $sender_id, $receiver_id, $utterance)
    {
        //Based on the scene retrieve the two participants
        $scene = $this->getVertex($scene_id);
        $participants = $scene->getVerticesEdgeTo();

        $sender = $participants->getVertexId($scene_id . '_' . $sender_id);
        $receiver = $participants->getVertexId($scene_id . '_' . $receiver_id);

        $utterance_edge = $sender->createEdgeTo($receiver);
        $utterance_edge->setAttribute(SELF::UTTERANCE, $utterance);

        return $utterance_edge;
    }


}