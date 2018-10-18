<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\ConversationInstanceInterface;
use actsmart\actsmart\Utils\ComponentInterface;

/**
 * The conversation instance store interface
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

    public function retrieve($userId);

    public function delete($userId);
}
