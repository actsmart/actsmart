<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Controllers\Active\ActiveController;
use actsmart\actsmart\Stores\ConversationStore;
use actsmart\actsmart\Interpreters\Intent;

class GenericSlackController extends ActiveController
{
    const SLACK_ACTION_TYPE_BUTTON = 'button';
    const SLACK_ACTION_TYPE_MENU = 'menu';

    protected $slack_verification_token;

    /* Time in seconds that a conversation is still considered as ongoing */
    protected $conversation_timeout = null;

    /* @var actsmart\actsmart\Interpreters\InterpreterInterface $message_interpreter */
    protected $message_interpreter;

    /* @var actsmart\actsmart\Stores\ConversationStore $conversation_store */
    protected $conversation_store;

    public function __construct(Agent $agent, $slack_verification_token, $conversation_timeout = 300)
    {
        parent::__construct($agent);
        $this->slack_verification_token = $slack_verification_token;
        $this->$conversation_timeout = $conversation_timeout;
    }

    public function execute(SensorEvent $e = null)
    {
        // New message arrives
        // 1. Determine if part of existing conversation
        // Conversations are stored in an Amazon Dynamo DB keyed with
        // <userid><timestamp> - userid is the current user and we are looking for timestamps
        // that are larger than $current_timestamp - $conversation_timeout

        // 2a. If no new conversation ongoing we need to instantiate a new conversation.
        // Determine the intention of the opening message and identify a conversation with an
        // init scene that matches that. If we have a conversation match instantiate conversation
        // and reply with the next message in the sequence.
        if ($e->getSubject() == 'message') {
            /* @var actsmart\actsmart\Interpreters\Intent $intent */
            $intent = $this->message_interpreter->interpret($e);

            $this->conversation_store->getMatchingConversations($intent);
        }

            // 2b. If there is an ongoing conversation instantiate that, determine the current scene
        // the expected next utterance and match that against what was received to understand if we
        // can reply with the next utterance or we will need to push up to either scene or conversation
        // context for clarification.
    }

    public function setDefaultMessageInterpreter(InterpreterInterface $interpreter)
    {
        $this->message_interpreter = $interpreter;
    }

    public function setDefaultConversationStore(ConversationStore $conversation_store)
    {
        $this->conversation_store = $conversation_store;
    }
}