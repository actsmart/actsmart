<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;

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
     * @param Intent $intent
     * @return array | boolean
     */
    public function getMatchingConversations(SensorEvent $e, Intent $intent);

    /**
     * Returns a single match - the first conversation that matchs for now.
     *
     * @todo This should become more sophisticated than simply return the first
     * conversation.
     *
     * @param Intent $intent
     * @return mixed
     */
    public function getMatchingConversation(SensorEvent $e, Intent $intent);

    /**
     * Creates the conversations objects this store will deal with.
     * @return mixed
     */
    public function buildConversations();
}
