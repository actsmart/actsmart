<?php

namespace actsmart\actsmart\Conversations;

use Fhaculty\Graph\Graph as Graph;
use Fhaculty\Graph\Set\Edges as Edges;
use actsmart\actsmart\Interpreters\Intent;

/**
 * A conversation is a Graph structure that describes the possible utterances
 * participants in the conversation can exchange. Scenes within a conversation
 * represent specific states of the conversation that are supposed to lead to some
 * resolution. A resolution will either end the conversation or move the conversation
 * to a new Scene.
 *
 * The initial scene of each conversation has a vertex with id 'init' - this makes it simpler
 * to identify.
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
    const INITIAL_SCENE = 'init';

    private $conversation_template_id;

    /**
     * @return mixed
     */
    public function getConversationTemplateId()
    {
        return $this->conversation_template_id;
    }

    /**
     * @param mixed $conversation_template_id
     * @return Conversation
     */
    public function setConversationTemplateId($conversation_template_id)
    {
        $this->conversation_template_id = $conversation_template_id;
        return $this;
    }



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
    public function addUtterance($start_scene, $end_scene, $sender_id, $receiver_id, $sequence, Intent $intent = null, Message $message = null, $completes = false, $interpreter = null)
    {
        $sender = $this->getParticipantToScene($start_scene, $sender_id);
        $receiver = $this->getParticipantToScene($end_scene, $receiver_id);

        /* @var actsmart\actsmart\Conversations\Utterance $utterance */
        $utterance = $sender->talksTo($receiver, $sequence);

        if (isset($message)) $utterance->setMessage($message);

        if (isset($intent)) $utterance->setIntent($intent);

        if (isset($interpreter)) $utterance->setInterpreter($interpreter);

        $utterance->setCompletes($completes);

        return $this;
    }

    /**
     * @param $scene_id
     * @return mixed
     */
    public function getAllUtterancesForScene($scene_id)
    {
        return $this->getScene($scene_id)->getAllUtterances();
    }

    public function getInitialScene()
    {
        return $this->getScene(SELF::INITIAL_SCENE);
    }

    public function addPreconditionToScene($scene_id, Condition $condition)
    {
        $this->getScene($scene_id)->addPrecondition($condition);
        return $this;
    }


}