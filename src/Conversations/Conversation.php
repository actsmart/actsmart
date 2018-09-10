<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Agent;
use Ds\Map;
use Fhaculty\Graph\Graph as Graph;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Sensors\SensorEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

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

    private $sequence = 0;

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
        return $this->getVertices()->getVerticesMatch(function ($scene) {
            if ($scene instanceof Scene) {
                return $scene;
            }
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
     * @param array $options
     * @return $this
     */
    public function addUtterance($options)
    {
        // @todo this is just minimally checking options for now.
        $utterance = null;

        if (isset($options['scene'])) {
            $sender = $this->getParticipantToScene($options['scene'], $options['sender']);
            $receiver = $this->getParticipantToScene($options['scene'], $options['receiver']);

            /* @var actsmart\actsmart\Conversations\Utterance $utterance */
            $utterance = $sender->talksTo($receiver, $this->sequence);
            $this->sequence++;
        }

        if (isset($options['starting_scene']) && isset($options['ending_scene'])) {
            $sender = $this->getParticipantToScene($options['starting_scene'], $options['sender']);
            $receiver = $this->getParticipantToScene($options['ending_scene'], $options['receiver']);

            /* @var actsmart\actsmart\Conversations\Utterance $utterance */
            $utterance = $sender->talksTo($receiver, $this->sequence);
            $this->sequence++;
        }


        if (isset($utterance)) {
            if (isset($options['message'])) {
                $utterance->setMessage($options['message']);
            }

            if (isset($options['intent'])) {
                $utterance->setIntent($options['intent']);
            }

            if (isset($options['interpreter'])) {
                $utterance->setInterpreter($options['interpreter']);
            }

            if (isset($options['action'])) {
                $utterance->setAction($options['action']);
            }

            if (isset($options['completes'])) {
                $utterance->setCompletes($options['completes']);
            }

            if (isset($options['preconditions'])) {
                foreach ($options['preconditions'] as $precondition) {
                    $utterance->addPrecondition($precondition);
                }
            }
        }

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

    /**
     * @return \Fhaculty\Graph\Vertex
     */
    public function getInitialScene()
    {
        return $this->getScene(SELF::INITIAL_SCENE);
    }

    /**
     * @param $scene_id
     * @param $condition
     * @return $this
     */
    public function addPreconditionToScene($scene_id, $condition)
    {
        $this->getScene($scene_id)->addPrecondition($condition);
        return $this;
    }

    /**
     * Returns possible followups based on sequence number supplied.
     *
     * Given a current utterance from a sender to a receiver the possible followups
     * are all the replies within the scene from the receiver to the sender.
     *
     * @param $current_sequence
     * @param $current_scene
     * @param GenericEvent $e
     * @param Agent $agent
     * @return array
     */
    public function getPossibleFollowUps(Agent $agent, $current_sequence, $current_scene, Map $source_utterance)
    {
        $current_utterance = $this->getUtteranceWithSequence($current_sequence);

        $current_sender = $current_utterance->getSender();

        $possible_followups = [];

        // We are interested in utterances where the receiver of the current utterance is replying to the
        // current sender.
        $sender_receiver_tracker = $current_utterance->getReceiver() . $current_utterance->getSender();

        foreach ($this->getAllUtterancesKeyedBySequenceForScene($current_scene) as $utterance) {
            // If we are dealing with utterances before the current utterance just skip them
            // @todo There should be a better way to get all the utterances after a certain sequence number.
            if ($utterance->getSequence() <= $current_sequence) {
                continue;
            }

            $sender_receiver_control = $utterance->getSender() . $utterance->getReceiver();

            // If we reached utterances where the sender and receiver are not what we expect get out.
            if ($sender_receiver_control != $sender_receiver_tracker) {
                break;
            }

            // Now we are dealing with utterances that are after the current utterance and the receiver of the
            // current utterance is replying to the sender of that utterance.
            if (($utterance->getSender() != $current_sender) &&
                $utterance->getReceiver() == $current_sender) {
                $possible_followups[$utterance->getSequence()] = $utterance;
            }
        }

        $followups_with_matching_preconditions = [];

        // Before sending followups onwards check their preconditions
        foreach ($possible_followups as $followup) {
            if ($agent->checkIntentConditions($followup->getPreconditions(), $source_utterance)) {
                $followups_with_matching_preconditions[$followup->getSequence()] = $followup;
            }
        }

        return $followups_with_matching_preconditions;
    }

    /**
     * Matching utterances are the ones that are a possible followup and match the intention.
     *
     * @param $current_sequence
     * @param SensorEvent $e
     * @param Intent $default_intent
     * @return array
     */
    public function getMatchingUtterances(Agent $agent, $current_scene, $current_sequence, Map $source_utterance, $default_intent = null)
    {
        // Check each possible followup for a match
        $matching_followups = [];

        //@todo if we are checking against what the bot should say then matching intents might not be useful
        foreach ($this->getPossibleFollowUps($agent, $current_sequence, $current_scene, $source_utterance) as $followup) {
            if ($followup->hasInterpreter()) {
                if ($followup->intentMatches($followup->interpret($source_utterance))) {
                    $matching_followups[] = $followup;
                }
            } else {
                if ($followup->intentMatches($default_intent)) {
                    $matching_followups[] = $followup;
                }
            }
        }

        return $matching_followups;
    }

    /**
     * @param $sequence
     * @param SensorEvent $e
     * @param Intent $default_intent
     * @return bool
     */
    public function getNextUtterance(Agent $agent, $current_scene, $sequence, Map $source_utterance, Intent $default_intent, $ongoing = true)
    {
        $matching_utterances = $ongoing ? $this->getMatchingUtterances($agent, $current_scene, $sequence, $source_utterance, $default_intent)
            :$this->getPossibleFollowUps($agent, $sequence, $current_scene, $source_utterance);

        // We couldn't find any matching intent. Get out.
        if (count($matching_utterances) == 0) {
            return false;
        }

        // For now we will just return the first of the matching utterances.
        $matching_utterances = array_reverse(($matching_utterances));
        return array_pop($matching_utterances);
    }


    public function getUtteranceWithSequence($sequence)
    {
        $utterances = $this->getAllUtterancesKeyedBySequence();

        if (isset($utterances[$sequence])) {
            return $utterances[$sequence];
        }

        return false;
    }

    public function getAllUtterancesKeyedBySequence()
    {
        $scenes = $this->getScenes();

        $utterances = [];

        foreach ($scenes as $scene) {
            $utterances = $utterances + $scene->getAllUtterancesKeyedBySequence();
        }
        ksort($utterances);
        return $utterances;
    }

    public function getAllUtterancesKeyedBySequenceForScene($scene_id)
    {
        return $this->getScene($scene_id)->getAllUtterancesKeyedBySequence();
    }
}
