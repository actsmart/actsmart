<?php

namespace actsmart\actsmart\Controllers;

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
     * @param Map $utterance
     * @param $intent
     * @return mixed
     */
    protected function getMatchingConversationId(Map $utterance, $intent)
    {
        /** @var ConversationTemplateStore $store */
        $store = $this->getAgent()->getStore('store.conversation_templates');
        $matchingConversationId = $store->getMatchingConversation($utterance, $intent);
        return $matchingConversationId;
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
        $ci->setCurrentUtteranceSequenceId($nextUtterance->getSequence());
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
}