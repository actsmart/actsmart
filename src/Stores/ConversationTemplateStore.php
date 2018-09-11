<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

abstract class ConversationTemplateStore implements ConversationTemplateStoreInterface, ComponentInterface, StoreInterface
{
    use ComponentTrait;

    protected $conversations = [];

    /**
     * @param Conversation $conversation
     */
    public function addConversation(Conversation $conversation)
    {
        $this->conversations[$conversation->getConversationTemplateId()] = $conversation;
    }

    /**
     * @param array $conversations
     */
    public function addConversations($conversations)
    {
        $this->conversations = $conversations;
    }

    public function getConversation($conversation_template_id)
    {
        return $this->conversations[$conversation_template_id];
    }

    /**
     * Returns the set of conversations whose opening utterance has an intent that
     * matches the $intent.
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return array | boolean
     */
    public function getMatchingConversations(Map $utterance, Intent $intent)
    {
        $matches = [];
        foreach ($this->conversations as $conversation) {
            $scene = $conversation->getInitialScene();

            // Check preconditions and if good then check interpreter
            if ($this->getAgent()->checkIntentConditions($scene->getPreConditions(), $utterance)) {
                $u = $conversation->getInitialScene()->getInitialUtterance();

                // TODO - we are overwriting the original Intent here and if the conversations that follow do not have their own interpreter, it doesn't get changed back
                if ($u->hasInterpreter()) {
                    $conversationIntent = $this->getAgent()->interpretIntent($u->getInterpreter(), $utterance);
                } else {
                    $conversationIntent = $intent;
                }

                if ($u->intentMatches($conversationIntent)) {
                    $matches[$conversation->getConversationTemplateId()] = $conversation;
                }
            }
        }

        if (count($matches) > 0) {
            return array_keys($matches);
        }

        return false;
    }

    /**
     * Returns a single match - the first conversation that matchs for now.
     *
     * @todo This should become more sophisticated than simply return the first
     * conversation.
     *
     * @param Intent $intent
     * @return mixed
     */
    public function getMatchingConversation(Map $utterance, Intent $intent)
    {
        $matches = $this->getMatchingConversations($utterance, $intent);

        if (!$matches) {
            return false;
        }

        // Have to do below to avoid a PHP_STRICT error for variables passed by reference
        // when operation happens in a single pass.
        $reversed_matches = array_reverse($matches);
        $match = array_pop($reversed_matches);

        return $match;
    }
}
