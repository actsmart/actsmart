<?php

namespace actsmart\actsmart\Controllers;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\Utterance;
use actsmart\actsmart\Conversations\WebChat\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Stores\ConversationTemplateStore;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

/**
 * The Base Conversation Controller that holds logic generic to all conversation controllers
 */
abstract class BaseConversationController implements ComponentInterface, ListenerInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait, ListenerTrait;

    /**
     * Gets a conversation matching the provided intent the intent that comes out of a custom intent interpreter for
     * conversations that have one.
     *
     * @param Map $utterance
     * @param $intent
     * @return Conversation
     */
    protected function getMatchingConversation(Map $utterance, $intent)
    {
        /** @var ConversationTemplateStore $store */
        $store = $this->getAgent()->getStore('store.conversation_templates');
        $matchingConversation = $store->getMatchingConversation($utterance, $intent);
        return $matchingConversation;
    }

    /**
     * Creates a NoMatch Intent
     *
     * @param Map $utterance
     * @return Intent
     */
    protected function noMatchIntent(Map $utterance): Intent
    {
        $intent = new Intent('NoMatch', $utterance, 1);
        return $intent;
    }

    /**
     * Saves/updates the conversation instance in the conversation instance store
     *
     * @param $ci ConversationInstance
     * @param $nextUtterance Utterance
     */
    protected function saveConversationInstance($ci, $nextUtterance)
    {
        $ci->setUpdateTs(new \DateTime());
        if ($nextUtterance->isRepeating()) {
            $ci->setCurrentUtteranceSequenceId($ci->getPreviousUtteranceId());
        } else {
            $ci->setCurrentUtteranceSequenceId($nextUtterance->getSequence());
        }
        $ci->setCompleting($nextUtterance->isCompleting());
        $this->getAgent()->getConversationInstanceStore()->save($ci);
    }

    /**
     * Gets an ongoing conversation from the conversation instance store if there is one
     *
     * @param Map $utterance
     * @return ConversationInstance
     */
    protected function ongoingConversation(Map $utterance)
    {
        $userId = $utterance->get(Literals::USER_ID);
        return $this->agent->getConversationInstanceStore()->retrieve($userId);
    }

    /**
     * Stores the context of the current message
     *
     * @param Utterance $nextUtterance
     * @param ConversationInstance $conversationInstance
     */
    protected function storeContext(Utterance $nextUtterance, ConversationInstance $conversationInstance)
    {
        $previousUtterance = $this->getPreviousUtteranceWithIntent($nextUtterance, $conversationInstance);
        
        $this->getAgent()->saveContextInformation('matched_intent', $previousUtterance->getIntent());

        $this->getAgent()->saveContextInformation('scene_id', $conversationInstance->getCurrentSceneId());

        $this->getAgent()->saveContextInformation('conversation_id', $conversationInstance->getConversationTemplateId());
    }

    /**
     * @param Utterance $nextUtterance
     * @param ConversationInstance $conversationInstance
     * @return Utterance|bool
     */
    protected function getPreviousUtteranceWithIntent(Utterance $nextUtterance, ConversationInstance $conversationInstance)
    {
        for ($i = $nextUtterance->getSequence(); $i >= 0; $i--) {
            $previousUtterance = $conversationInstance->getConversation()->getUtteranceWithSequence($i);
            if ($previousUtterance->getIntent() !== null) {
                return $previousUtterance;
            }
        }
        return null;
    }
}