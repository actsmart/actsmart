<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\ConversationInstanceInterface;
use actsmart\actsmart\Utils\ComponentTrait;

abstract class BaseConversationInstanceStore implements ConversationInstanceStoreInterface
{
    const KEY = 'store.conversation_instance';

    use ComponentTrait;

    public function getKey()
    {
        return static::KEY;
    }

    abstract public function save(ConversationInstanceInterface $conversationInstance);

    abstract public function retrieve($userId, $completingConversations = false);
    
    abstract public function delete($userId);
}