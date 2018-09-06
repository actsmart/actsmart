<?php
/**
 * Created by PhpStorm.
 * User: stuarthaigh
 * Date: 17/05/2018
 * Time: 11:52
 */

namespace actsmart\actsmart\Conversations;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Conversations\Slack\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Stores\ConversationTemplateStore;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

interface ConversationInstanceInterface
{
    /**
     * Setups a conversation at the initial scene and utterance.
     */
    public function initConversation();

    public function setConversation();

    public function getConversation();

    public function setCurrentUtteranceSequenceId($current_utterance_sequence_id);

    public function getCurrentUtteranceSequenceId();

    public function getCurrentUtterance();

    public function getCurrentAction();

    /**
     * @return ConversationTemplateStore
     */
    public function getConversationStore();

    /**
     * @param ConversationTemplateStore $conversation_store
     * @return ConversationInstance
     */
    public function setConversationStore($conversation_store);

    /**
     * @return string
     */
    public function getConversationTemplateId();

    /**
     * @param string $conversation_template_id
     * @return ConversationInstance
     */
    public function setConversationTemplateId($conversation_template_id);

    /**
     * @return mixed
     */
    public function getWorkspaceId();

    /**
     * @param mixed $workspace_id
     * @return ConversationInstance
     */
    public function setWorkspaceId($workspace_id);

    /**
     * @return mixed
     */
    public function getUserId();

    /**
     * @param mixed $user_id
     * @return ConversationInstance
     */
    public function setUserId($user_id);

    /**
     * @return mixed
     */
    public function getStartTs();

    /**
     * @param mixed $start_ts
     * @return ConversationInstance
     */
    public function setStartTs($start_ts);

    /**
     * @return mixed
     */
    public function getUpdateTs();

    /**
     * @param mixed $update_ts
     * @return ConversationInstance
     */
    public function setUpdateTs($update_ts);

    /**
     * @return mixed
     */
    public function getChannelId();

    /**
     * @param mixed $channel_id
     */
    public function setChannelId($channel_id);

    /**
     * @return mixed
     */
    public function getCurrentSceneId();

    /**
     * @param mixed $current_scene_id
     * @return ConversationInstance
     */
    public function setCurrentSceneId($current_scene_id);

    public function saveConversationInstance();

    public function getNextUtterance(Agent $agent, Map $source_utterance, Intent $default_intent, $ongoing = true);
}