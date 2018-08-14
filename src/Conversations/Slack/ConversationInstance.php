<?php

namespace actsmart\actsmart\Conversations\Slack;

use actsmart\actsmart\Conversations\BaseConversationalInstance;
use actsmart\actsmart\Conversations\ConversationInstanceInterface;
use actsmart\actsmart\Stores\ConversationTemplateStore;

class ConversationInstance extends BaseConversationalInstance
{
    /**
     * The workspace id where this conversation is taking place.
     */
    private $workspace_id;

    /**
     * The channel id where the conversation is happening.
     */
    private $channel_id;

    public function __construct($conversation_template_id = null, ConversationTemplateStore $conversation_store, $workspace_id, $user_id, $channel_id, $start_ts = 0, $update_ts = 0)
    {
        parent::__construct($conversation_template_id, $conversation_store, $user_id, $start_ts, $update_ts);
        $this->workspace_id = $workspace_id;
        $this->channel_id = $channel_id;
    }

    /**
     * @return mixed
     */
    public function getWorkspaceId()
    {
        return $this->workspace_id;
    }

    /**
     * @param mixed $workspace_id
     * @return ConversationInstance
     */
    public function setWorkspaceId($workspace_id)
    {
        $this->workspace_id = $workspace_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getChannelId()
    {
        return $this->channel_id;
    }

    /**
     * @param mixed $channel_id
     * @return ConversationInstanceInterface
     */
    public function setChannelId($channel_id)
    {
        $this->channel_id = $channel_id;
        return $this;
    }
}
