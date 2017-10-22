<?php

namespace actsmart\actsmart\Conversations;

use Fhaculty\Graph\Graph as Graph;
use Fhaculty\Graph\Set\Edges as Edges;

/**
 * A conversation is a Graph structure that describes the possible utterances
 * participants in the conversation can exchange. Scenes within a conversation
 * represent specific states of the conversation that are supposed to lead to some
 * resolution. A resolution will either end the conversation or move the conversation
 * to a new Scene.
 *
 * Class Conversation
 * @package actsmart\actsmart\Conversations
 */
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
     * Creates a new Scene which will point to the participants,
     * useful as a structure to easily access participants within a Scene.
     * @param $scene_id
     * @return $this
     */
    public function createScene($scene_id)
    {
        new Scene($this, $scene_id);
        return $this;
    }

    /**
     * Retrieve all Scenes associates with this conversation.
     *
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

    /**
     * @param $scene_id
     * @param $participant_id
     * @return Participant
     */
    public function createParticipant($scene_id, $participant_id)
    {
        return new Participant($this, $scene_id, $participant_id);
    }

    /**
     * @param $scene_id
     * @return mixed
     */
    public function getParticipantsToScene($scene_id)
    {
        return $this->getScene($scene_id)->getParticipants();
    }

    /**
     * @param $scene_id
     * @param $participant_id
     * @return mixed
     */
    public function getParticipantToScene($scene_id, $participant_id)
    {
        return $this->getScene($scene_id)->getParticipant($participant_id);
    }

    /**
     * @param $scene_id
     * @param $participant_id
     * @return $this
     */
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
     * conversation. If start and end scene are the same we are dealing with a simple
     * utterance while if start scene and end scene are different we are dealing with an
     * exit Utterance which moves the conversation to a new Scene.
     *
     * @param $start_scene
     * @param $end_scene
     * @param $sender_id
     * @param $receiver_id
     * @param Message $message
     * @param $sequence - the overall expected order of this message in a conversation.
     * @return $this
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