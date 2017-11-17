<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
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
     * @param Intent $intent
     * @return array | boolean
     */
    public function getMatchingConversations(GenericEvent $e, Intent $intent)
    {
        $matches = [];
        foreach ($this->conversations as $conversation) {
            $scene = $conversation->getInitialScene();

            // Check preconditions and if good then check interpreter
            if ($this->getAgent()->checkConditions($scene->getPreConditions(), $e)) {
                $u = $conversation->getInitialScene()->getInitialUtterance();

                if ($u->hasInterpreter()) {
                    $intent = $u->interpret($e);
                }

                if ($u->intentMatches($intent)) {
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
    public function getMatchingConversation(GenericEvent $e, Intent $intent)
    {
        $matches = $this->getMatchingConversations($e, $intent);

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
