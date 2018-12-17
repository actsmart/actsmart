<?php

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Stores\ConversationTemplateStore;
use Ds\Map;

abstract class BaseConversationalInstance implements ConversationInstanceInterface
{
    /* @var Conversation $conversation */
    protected $conversation;

    /* @var ConversationTemplateStore $conversationStore */
    protected $conversationStore;

    /**
     * @var string - an identified for the conversation template
     */
    protected $conversationTemplateId;

    /**
     * When the conversation started.
     */
    protected $startTs;

    /**
     * When a conversation was updated.
     */
    protected $updateTs;

    /**
     * Current scene id.
     */
    protected $currentSceneId;

    /**
     * Current utterance id.
     */
    protected $currentUtteranceSequenceId;

    /**
     * Previous utterance id.
     */
    protected $previousUtteranceId;
    
    /**
     * The user id the bot is having the conversation with.
     */
    protected $userId;

    /** @var bool Whether the next utterance is completing or not */
    protected $completing = false;

    public function __construct($conversationTemplateId = null, ConversationTemplateStore $conversationStore, $userId, $startTs = 0, $updateTs = 0)
    {
        $this->conversationTemplateId = $conversationTemplateId;
        $this->conversationStore = $conversationStore;
        $this->startTs = $startTs;
        $this->updateTs = $updateTs;
        $this->userId = $userId;
    }

    /**
     * Setups a conversation at the initial scene and utterance.
     */
    public function initConversation()
    {
        // Get the relevant conversation template
        $this->conversation = $this->conversationStore->getConversation($this->conversationTemplateId);

        /* @var \actsmart\actsmart\Conversations\Scene $initialScene */
        $initialScene = $this->conversation->getInitialScene();

        /* @var \actsmart\actsmart\Conversations\Utterance $initialUtterance */
        $initialUtterance = $initialScene->getInitialUtterance();

        // Setup the current scene and the current utterance.
        $this->setCurrentSceneId($initialUtterance->getEndScene());
        $this->setCurrentUtteranceSequenceId($initialUtterance->getSequence());
        $this->setPreviousUtteranceId($initialUtterance->getSequence());
    }

    public function setConversation()
    {
        $this->conversation = $this->conversationStore->getConversation($this->conversationTemplateId);
    }

    public function getConversation()
    {
        return $this->conversation;
    }

    /**
     * @inheritdoc
     */
    public function setCurrentUtteranceSequenceId($currentUtteranceSequenceId)
    {
        $this->currentUtteranceSequenceId = $currentUtteranceSequenceId;
        return $this;
    }

    public function getCurrentUtteranceSequenceId()
    {
        return $this->currentUtteranceSequenceId;
    }

    /**
     * @return mixed
     */
    public function getPreviousUtteranceId()
    {
        return $this->previousUtteranceId;
    }

    /**
     * @param mixed $previousUtteranceId
     */
    public function setPreviousUtteranceId($previousUtteranceId): void
    {
        $this->previousUtteranceId = $previousUtteranceId;
    }

    /**
     * @return Utterance
     */
    public function getCurrentUtterance()
    {
        return $this->conversation->getUtteranceWithSequence($this->currentUtteranceSequenceId);
    }

    public function getCurrentAction()
    {
        return $this->getCurrentUtterance()->getAction();
    }

    /**
     * Gets the information request on the current Utterance if there is one
     */
    public function getCurrentInformationRequest()
    {
        return $this->getCurrentUtterance()->getInformationRequest();
    }

    /**
     * @return ConversationTemplateStore
     */
    public function getConversationStore()
    {
        return $this->conversationStore;
    }

    /**
     * @param ConversationTemplateStore $conversationStore
     * @return ConversationInstanceInterface
     */
    public function setConversationStore($conversationStore)
    {
        $this->conversationStore = $conversationStore;
        return $this;
    }

    /**
     * @return string
     */
    public function getConversationTemplateId()
    {
        return $this->conversationTemplateId;
    }

    /**
     * @param string $conversationTemplateId
     * @return ConversationInstanceInterface
     */
    public function setConversationTemplateId($conversationTemplateId)
    {
        $this->conversationTemplateId = $conversationTemplateId;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStartTs()
    {
        return $this->startTs;
    }

    /**
     * @param mixed $startTs
     * @return ConversationInstanceInterface
     */
    public function setStartTs($startTs)
    {
        $this->startTs = $startTs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUpdateTs()
    {
        return $this->updateTs;
    }

    /**
     * @param mixed $updateTs
     * @return ConversationInstanceInterface
     */
    public function setUpdateTs($updateTs)
    {
        $this->updateTs = $updateTs;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param mixed $userId
     * @return ConversationInstanceInterface
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCompleting(): bool
    {
        return $this->completing;
    }

    /**
     * @param bool $completing
     */
    public function setCompleting(bool $completing)
    {
        $this->completing = $completing;
    }

    /**
     * @param Agent $agent
     * @param Map $sourceUtterance
     * @param Intent $defaultIntent
     * @param bool $ongoing
     * @return Utterance|false
     */
    public function getNextUtterance(Agent $agent, Map $sourceUtterance, Intent $defaultIntent, $ongoing = true)
    {
        // If the conversation is not ongoing we are dealing with a new conversation and just need to get
        // the next thing the bot should say based on what the user just said.
        // @todo Variable names and structure of this is too confusing.

        if (!$ongoing) {
            return $this->conversation->getNextUtterance($agent, $this->currentSceneId, $this->currentUtteranceSequenceId, $sourceUtterance, $defaultIntent, $ongoing);
        }

        // If we are dealing with an ongoing conversation we first attempt to identify what the user's next utterance was.
        // The conversation model could support the user saying any number of things - so we need to get all of them,
        // interpret them, decide which one was actually said and the move the conversation forward based on that.
        $user_current_utterance = $this->conversation->getNextUtterance($agent, $this->currentSceneId, $this->currentUtteranceSequenceId, $sourceUtterance, $defaultIntent, $ongoing);

        if (!$user_current_utterance) {
            return false;
        }

        // Having determined what the user just said, let us move the conversation to point to that utterance.
        $this->currentSceneId = $user_current_utterance->getEndScene();
        $this->currentUtteranceSequenceId = $user_current_utterance->getSequence();

        // Now let us retrieve what the bot should reply given that user utterance. Treat this like a new conversation and just get the bot's next reply.
        $bot_next_utterance = $this->conversation->getNextUtterance($agent, $this->currentSceneId, $this->currentUtteranceSequenceId, $sourceUtterance, $defaultIntent, false);
        return $bot_next_utterance;
    }

    /**
     * @return mixed
     */
    public function getCurrentSceneId()
    {
        return $this->currentSceneId;
    }

    /**
     * @param mixed $currentSceneId
     * @return ConversationInstanceInterface
     */
    public function setCurrentSceneId($currentSceneId)
    {
        $this->currentSceneId = $currentSceneId;
        return $this;
    }
}
