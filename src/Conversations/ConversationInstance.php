<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Stores\ConversationStore;

class ConversationInstance
{
    /* @var actsmart/actsmart/Conversations/Conversation $conversation */
    private $conversation;

    /**
     * @var string - an identified for the conversation template
     */
    private $conversation_template_id;

    /**
     * The workspace id where this conversation is taking place.
     */
    private $workspace_id;

    /**
     * The user id the bot is having the conversation with.
     */
    private $user_id;

    /**
     * When the conversation started.
     */
    private $start_ts;

    /**
     * When a conversation was updated.
     */
    private $update_ts;

    /**
     * Current scene id.
     */
    private $current_scene_id;

    /**
     * Current utterance id.
     */
    private $current_utterance_sequence_id;

    public function __construct($conversation_template_id, ConversationStore $conversation_store, $workspace_id, $user_id, $channel_id, $start_ts)
    {
        $this->conversation_template_id = $conversation_template_id;
        $this->conversation_store = $conversation_store;
        $this->workspace_id = $workspace_id;
        $this->user_id = $user_id;
        $this->start_ts = $start_ts;
    }

    public function initConversation()
    {
        // Get the relevant conversation template
        $this->conversation = $this->conversation_store->getConversation($this->conversation_template_id);

        /* @var actsmart\actsmart\Conversations\Scene $initial_scene */
        $initial_scene = $this->conversation->getInitialScene();

        // Setup the current scene and the current utterance.
        $this->setCurrentScene($initial_scene->getSceneId());
        $this->setCurrentUtteranceSequenceId($initial_scene->getInitialUtterance()->getSequence());
    }

    public function getNextUtterance()
    {
        /* @var actsmart\actsmart\Conversations\Scene $scene */
        $scene = $this->conversation->getScene($this->current_scene_id);
        $utterances = $scene->getAllUtterances();

        return $utterances->getEdgeIndex($this->current_utterance_sequence_id+1);
    }

    public function getConversation()
    {
        return $this->conversation;
    }

    public function setCurrentScene($current_scene_id)
    {
        $this->current_scene_id = $current_scene_id;
    }

    public function setCurrentUtteranceSequenceId($current_utterance_sequence_id)
    {
        $this->current_utterance_sequence_id = $current_utterance_sequence_id;
    }
}