<?php

namespace actsmart\actsmart\Conversations;


class ConversationInstance
{
    /* @var actsmart/actsmart/Conversations/Conversation $conversation */
    private $conversation;

    /**
     * The workspace id where this conversation is taking place.
     */
    private $workspace_id;

    /**
     * The user id the bot is having the conversation with.
     */
    private $user_id;

    /**
     * When the conversation started.
     */
    private $start_ts;

    /**
     * When a conversation was updated.
     */
    private $update_ts;

    /**
     * Current scene id.
     */
    private $current_scene_id;

    /**
     * Current utterance id.
     */
    private $current_utterance_id;

    public function __construct($workspace_id, $user_id, $start_ts)
    {
        $this->workspace_id = $workspace_id;
        $this->user_id = $user_id;
        $this->start_ts = $start_ts;
    }

    public function buildConversation($conversation_template)
    {

    }
}