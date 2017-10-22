<?php

namespace actsmart\actsmart\Conversations;

use Fhaculty\Graph\Graph as Graph;
use Fhaculty\Graph\Set\Edges as Edges;

class Conversation extends Graph
{

    //@todo Could use an attributebag for these.
    const UTTERANCE = 'utterance';
    const SEQUENCE = 'sequence';
    const TYPE = 'type';
    const SCENE = 'scene';
    const PARTICIPANT = 'participant';
    const PARTICIPATING = 'participating';
    const ID = 'id';

    /**
     * @param $scene_id
     * @return $this
     */
    public function createScene($scene_id)
    {
        $scene = new Scene($this, $scene_id);
        return $this;
    }

    /**
     * @return \Fhaculty\Graph\Set\Vertices
     */
    public function getScenes()
    {
        return $this->getVertices()->getVerticesMatch(function($scene) {
           if ($scene instanceof Scene) return $scene;
        });
    }

    /**
     * @param $scene_id
     * @return \Fhaculty\Graph\Vertex
     */
    public function getScene($scene_id)
    {
        return $this->getVertex($scene_id);
    }

    public function createParticipant($scene_id, $participant_id)
    {
        return new Participant($this, $scene_id, $participant_id);
    }

    public function getParticipantsToScene($scene_id)
    {
        return $this->getScene($scene_id)->getParticipants();
    }

    public function getParticipantToScene($scene_id, $participant_id)
    {
        return $this->getScene($scene_id)->getParticipant($participant_id);
    }

    public function addParticipantToScene($scene_id, $participant_id)
    {
        // When adding a participant their id is prefixed with the scene_id indicating a
        // specific distinguished stage of the dialog (aka a scene).
        $participant = $this->createParticipant($scene_id, $participant_id);

        // Get the scene and connect the participant to the scene
        $this->getScene($scene_id)
            ->createEdgeTo($participant)
            ->setAttribute(SELF::TYPE, SELF::PARTICIPATING);

        return $this;
    }

    /**
     * An utterance is an edge between two vertices that are participants to a
     * conversation. A simple utterance keeps participants within the same scene.
     */
    public function addUtterance($start_scene, $end_scene, $sender_id, $receiver_id, Message $message, $sequence)
    {
        $sender = $this->getParticipantToScene($start_scene, $sender_id);
        $receiver = $this->getParticipantToScene($end_scene, $receiver_id);

        $utterance = $sender->talksTo($receiver, $sequence);
        $utterance->addMessage($message);

        return $this;
    }

    public function getAllUtterancesForScene($scene_id)
    {
        return $this->getScene($scene_id)->getAllUtterances();
    }

    public function setCurrentUtterance($utterance)
    {

    }


}