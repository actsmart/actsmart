<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Interpreters\Intent;
use Ds\Map;

interface ConversationTemplateStoreInterface
{

    /**
     * @param Conversation $conversation
     */
    public function addConversation(Conversation $conversation);

    /**
     * @param array $conversations
     */
    public function addConversations($conversations);

    /**
     * @param $conversation_template_id
     * @return Conversation
     */
    public function getConversation($conversation_template_id);

    /**
     * Returns the set of conversations whose opening utterance has an intent that
     * matches the $intent.
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return array | boolean
     */
    public function getMatchingConversations(Map $utterance, Intent $intent);

    /**
     * Returns a single match - the first conversation that matches for now.
     *
     * @todo This should become more sophisticated than simply return the first
     * conversation.
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return mixed
     */
    public function getMatchingConversation(Map $utterance, Intent $intent);

    /**
     * Creates the conversations objects this store will deal with.
     * @return mixed
     */
    public function buildConversations();
}
