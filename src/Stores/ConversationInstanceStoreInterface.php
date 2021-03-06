<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\ConversationInstanceInterface;
use actsmart\actsmart\Utils\ComponentInterface;

/**
 * The conversation instance store interface
 * TODO this needs to use channels as well as userId
 */
interface ConversationInstanceStoreInterface extends ComponentInterface
{
    /**
     * Should save in an upsert fashion so that any existing record is updated
     *
     * @param ConversationInstanceInterface $conversationInstance
     * @return mixed
     */
    public function save(ConversationInstanceInterface $conversationInstance);

    public function retrieve($userId, $completingConversations = false);

    public function delete($userId);
}
