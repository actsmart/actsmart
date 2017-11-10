<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Stores\ConversationTemplateStore;

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
     * The channel id where the conversation is happening.
     */
    private $channel_id;

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

    public function __construct($conversation_template_id = null, ConversationTemplateStore $conversation_store, $workspace_id, $user_id, $channel_id, $start_ts = 0, $update_ts = 0)
    {
        $this->conversation_template_id = $conversation_template_id;
        $this->conversation_store = $conversation_store;
        $this->workspace_id = $workspace_id;
        $this->channel_id = $channel_id;
        $this->user_id = $user_id;
        $this->start_ts = $start_ts;
        $this->update_ts = $update_ts;
    }

    /**
     * Setups a conversation at the initial scene and utterance.
     */
    public function initConversation()
    {
        // Get the relevant conversation template
        $this->conversation = $this->conversation_store->getConversation($this->conversation_template_id);

        /* @var actsmart\actsmart\Conversations\Scene $initial_scene */
        $initial_scene = $this->conversation->getInitialScene();

        // Setup the current scene and the current utterance.
        $this->setCurrentSceneId($initial_scene->getSceneId());
        $this->setCurrentUtteranceSequenceId($initial_scene->getInitialUtterance()->getSequence());
    }

    public function setConversation()
    {
        $this->conversation = $this->conversation_store->getConversation($this->conversation_template_id);
    }

    public function getConversation()
    {
        return $this->conversation;
    }

    public function setCurrentUtteranceSequenceId($current_utterance_sequence_id)
    {
        $this->current_utterance_sequence_id = $current_utterance_sequence_id;
        return $this;
    }

    public function getCurrentUtteranceSequenceId()
    {
        return $this->current_utterance_sequence_id;
    }

    public function getCurrentUtterance()
    {
        return $this->conversation->getUtteranceWithSequence($this->current_utterance_sequence_id);
    }

    public function performCurrentAction(SensorEvent $event)
    {
        $this->getCurrentUtterance()->performAction($event);
    }

    /**
     * @return ConversationTemplateStore
     */
    public function getConversationStore()
    {
        return $this->conversation_store;
    }

    /**
     * @param ConversationTemplateStore $conversation_store
     * @return ConversationInstance
     */
    public function setConversationStore($conversation_store)
    {
        $this->conversation_store = $conversation_store;
        return $this;
    }

    /**
     * @return string
     */
    public function getConversationTemplateId()
    {
        return $this->conversation_template_id;
    }

    /**
     * @param string $conversation_template_id
     * @return ConversationInstance
     */
    public function setConversationTemplateId($conversation_template_id)
    {
        $this->conversation_template_id = $conversation_template_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }

    /**
     * @param mixed $workspace_id
     * @return ConversationInstance
     */
    public function setWorkspaceId($workspace_id)
    {
        $this->workspace_id = $workspace_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * @param mixed $user_id
     * @return ConversationInstance
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTs()
    {
        return $this->start_ts;
    }

    /**
     * @param mixed $start_ts
     * @return ConversationInstance
     */
    public function setStartTs($start_ts)
    {
        $this->start_ts = $start_ts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateTs()
    {
        return $this->update_ts;
    }

    /**
     * @param mixed $update_ts
     * @return ConversationInstance
     */
    public function setUpdateTs($update_ts)
    {
        $this->update_ts = $update_ts;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channel_id;
    }

    /**
     * @param mixed $channel_id
     */
    public function setChannelId($channel_id)
    {
        $this->channel_id = $channel_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCurrentSceneId()
    {
        return $this->current_scene_id;
    }

    /**
     * @param mixed $current_scene_id
     * @return ConversationInstance
     */
    public function setCurrentSceneId($current_scene_id)
    {
        $this->current_scene_id = $current_scene_id;
        return $this;
    }

    public function saveConversationInstance()
    {
        $this->conversation_instance_store->save($this);
    }

    public function getNextUtterance(SensorEvent $e, Intent $default_intent, $ongoing = true)
    {
        // If the conversation is not ongoing we are dealing with a new conversation and just need to get
        // the next thing the bot should say based on what the user just said.
        // @todo Variable names and structure of this is too confusing.

        if (!$ongoing) {
            return $this->conversation->getNextUtterance($this->current_scene_id, $this->current_utterance_sequence_id, $e, $default_intent, $ongoing);
        }

        // If we are dealing with an ongoing conversation we first attempt to identify what the user's next utterance was.
        // The conversation model could support the user saying any number of things - so we need to get all of them,
        // interpret them, decide which one was actually said and the move the converation forward based on that.
        $user_current_utterance = $this->conversation->getNextUtterance($this->current_scene_id, $this->current_utterance_sequence_id, $e, $default_intent, $ongoing);

        if (!$user_current_utterance) {
            return false;
        }

        // Having determined what the user just said, let us move the conversation to point to that utterance.
        $this->current_scene_id = $user_current_utterance->getEndScene();
        $this->current_utterance_sequence_id = $user_current_utterance->getSequence();

        // Now let us retrieve what the bot should reply given that user utterance. Treat this like a new conversation and just get the bot's next reply.
        $bot_next_utterance = $this->conversation->getNextUtterance($this->current_scene_id, $this->current_utterance_sequence_id, $e, $default_intent, false);
        return $bot_next_utterance;
    }
}
