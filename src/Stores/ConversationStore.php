<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;

class ConversationStore implements StoreInterface
{

    private $conversations = [];

    public function __construct()
    {
        //
    }

    /**
     * @param Conversation $conversation
     */
    public function addConversation(Conversation $conversation)
    {
        $this->conversations[$conversation->getConversationTemplateId()] = $conversation;
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
     * @return array
     */
    public function getMatchingConversations(Intent $intent)
    {
        $matches = [];
        foreach ($this->conversations as $conversation)
        {
            $u = $conversation->getInitialScene()->getInitialUtterance();
            if ($u->intentMatches($intent)) $matches[$conversation->getConversationTemplateId()] = $conversation;
        }

        return array_keys($matches);
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
    public function getMatchingConversation(Intent $intent)
    {
        $matches = $this->getMatchingConversations($intent);
        // Have to do below to avoid a PHP_STRICT error for variables passed by reference
        // when operation happens in a single pass.
        $reversed_matches = array_reverse($matches);
        $match = array_pop($reversed_matches);

        return $match;
    }

    public function store($data)
    {
        
    }

    public function reply()
    {

    }

}