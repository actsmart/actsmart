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

    public function addConversation(Conversation $conversation)
    {
        $this->conversations[] = $conversation;
    }

    public function getMatchingConversations(Intent $intent)
    {
        $matches = [];
        foreach ($this->conversations as $conversation)
        {
            $u = $conversation->getInitialScene()->getInitialUtterance();
            if ($u->intentMatches($intent)) $matches[] = $conversation;
        }
        dd($matches);
        return $matches;
    }

    public function store($data)
    {
        
    }

    public function reply()
    {

    }

}