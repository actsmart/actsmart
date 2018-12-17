<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\Scene;
use actsmart\actsmart\Conversations\Utterance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use Ds\Map;

abstract class ConversationTemplateStore extends EphemeralStore
{
    const CUSTOM_INTERPRETER = "custom_interpreter";
    const DEFAULT_INTERPRETER = "default_interpreter";

    /**
     * @param Conversation $conversation
     */
    public function addConversation(Conversation $conversation)
    {
        $this->storeInformation(new ContextInformation(Conversation::INFORMATION_TYPE, $conversation->getConversationTemplateId(), $conversation));
    }

    /**
     * @param array $conversations
     */
    public function addConversations($conversations)
    {
        foreach ($conversations as $conversation) {
            $this->addConversation($conversation);
        }
    }

    /**
     * Returns all conversations (assumes there is just a single type);
     *
     * @return mixed
     */
    public function getAllConversations()
    {
        return $this->store->get(Conversation::INFORMATION_TYPE);
    }

    /**
     * @param $conversation_template_id
     * @return Conversation | null
     */
    public function getConversation($conversation_template_id)
    {
        /* @var ContextInformation $information */
        $information = $this->getInformation(Conversation::INFORMATION_TYPE, $conversation_template_id);
        return $information->getValue();
    }

    /**
     * Returns the set of conversations whose opening utterance has an intent that
     * matches the $intent.
     *
     * Will return matching conversations split by whether they used the default interpreter or a custom interpreter in
     * the format:
     *
     * [
     *   'custom_interpreter' => [
     *      {conversation},
     *      ...
     *   ],
     *   'default_interpreter' => [
     *      {conversation},
     *      ...
     *   ]
     * ]
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return array
     */
    public function getMatchingConversations(Map $utterance, Intent $intent)
    {
        $matches = [];
        /** @var Conversation $conversation */
        foreach ($this->getAllConversations() as $conversation_id => $conversation) {

            /** @var Scene $scene */
            $scene = $conversation->getInitialScene();

            // Check preconditions and if good then check interpreter
            if ($this->getAgent()->checkIntentConditions($scene->getPreconditions(), $utterance)) {
                /** @var Utterance $u */
                $u = $conversation->getInitialScene()->getInitialUtterance();

                if ($u->hasIntentInterpreter()) {
                    $conversationIntent = $this->getAgent()->interpretIntent($u->getIntentInterpreter(), $utterance);
                    $key = self::CUSTOM_INTERPRETER;
                } else {
                    $conversationIntent = $intent;
                    $key = self::DEFAULT_INTERPRETER;
                }

                if ($u->intentMatches($conversationIntent)) {
                    $matches[$key][] = $conversation;
                }
            }
        }

        if (count($matches) > 0) {
            return $matches;
        }

        return false;
    }

    /**
     * Returns a single matching conversation using this logic:
     *
     * If there are conversations that matched using a custom interpreter, discard conversations using the default
     * interpreter.
     * Return the conversation with the highest confidence score
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return mixed
     */
    public function getMatchingConversation(Map $utterance, Intent $intent)
    {
        $matches = $this->getMatchingConversations($utterance, $intent);

        if (!$matches) {
            return false;
        }

        if (isset($matches[self::CUSTOM_INTERPRETER])) {
            $matches = $matches[self::CUSTOM_INTERPRETER];
        } else {
            $matches = $matches[self::DEFAULT_INTERPRETER];
        }

        usort($matches, function (Conversation $a, Conversation $b) {
            $aConfidence = $a->getInitialScene()->getInitialUtterance()->getIntent()->getConfidence();
            $bConfidence = $b->getInitialScene()->getInitialUtterance()->getIntent()->getConfidence();

            if ($aConfidence == $bConfidence) {
                return 0;
            }

            return ($aConfidence < $bConfidence) ? -1 : 1;
        });

        return array_shift($matches);
    }

    abstract public function buildConversations();
}
