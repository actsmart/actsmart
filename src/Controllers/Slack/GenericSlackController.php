<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Agent;
use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Controllers\Active\ActiveController;
use actsmart\actsmart\Stores\ConversationTemplateStore;
use actsmart\actsmart\Stores\ConversationInstanceStore;
use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use Illuminate\Support\Facades\Log;

class GenericSlackController extends ActiveController
{
    const SLACK_ACTION_TYPE_BUTTON = 'button';
    const SLACK_ACTION_TYPE_MENU = 'menu';

    protected $slack_verification_token;

    /* Time in seconds that a conversation is still considered as ongoing */
    // @todo - This is not actually used right now.
    protected $conversation_timeout = null;

    /* @var actsmart\actsmart\Interpreters\InterpreterInterface $message_interpreter */
    protected $message_interpreter;

    /* @var actsmart\actsmart\Stores\ConversationTemplateStore $conversation_store */
    protected $conversation_store;

    /* @var actsmart\actsmart\Stores\ConversationInstanceStore $conversation_instance_store */
    protected $conversation_instance_store;


    public function __construct(Agent $agent, $slack_verification_token, $conversation_timeout = 300)
    {
        parent::__construct($agent);
        $this->slack_verification_token = $slack_verification_token;
        $this->$conversation_timeout = $conversation_timeout;
    }

    public function execute(SensorEvent $e = null)
    {
        if ($e->getSubject() == 'message') {
            if (!$this->handleOngoingConversation($e)) {
                if (!$this->handleNewConversation($e)) $this->handleNothingMatched($e);
            }
        }
    }

    public function handleOngoingConversation(SensorEvent $e)
    {
        // If we don't get a CI object back there is no ongoing conversation that matches. Bail out.
        if (!$ci=$this->ongoingConversation($e)) return false;

        // We have a conversation template id so let's thaw the actual conversation graph. We only
        // have the id before this point.
        $ci->setConversation();

        $current_scene = $ci->getConversation()->getScene($ci->getCurrentSceneId());

        // Set up a default intent
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $default_intent = $this->message_interpreter->interpret($e);

        if (!$next_utterance = $current_scene->getNextUtterance($ci->getCurrentUtteranceSequenceId(), $e, $default_intent)) return false;

        $response = $this->actuators['slack.actuator']->postMessage($next_utterance->getMessage()->getSlackMessage($e));


        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        // @todo if an ongoing conversation finishes we have to get rid of the record on Dynamo!
        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->conversation_instance_store->save($ci);
        return true;
    }

    public function handleNewConversation(SensorEvent $e)
    {
        var_dump('new');
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = $this->message_interpreter->interpret($e);
        var_dump($intent);

        $matching_conversation_id = $this->conversation_store->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) return false;

        $ci = new ConversationInstance($matching_conversation_id,
            $this->conversation_store,
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId(),
            $e->getTimestamp(),
            $e->getTimestamp());

        /* @var actsmart\actsmart\Conversations\Conversation $conversation */
        $conversation = $ci->initConversation();

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance();

        $response = $this->actuators['slack.actuator']->postMessage($next_utterance->getMessage()->getSlackMessage($e));

        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        if ($next_utterance->isCompleting()) {
            var_dump('It completes');
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->conversation_instance_store->save($ci);
        var_dump('new true');
        return true;
    }

    public function handleNothingMatched($e)
    {
        var_dump('nothing matched');
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch');

        $matching_conversation_id = $this->conversation_store->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) return false;

        $ci = new ConversationInstance($matching_conversation_id,
            $this->conversation_store,
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId(),
            $e->getTimestamp(),
            $e->getTimestamp());

        $conversation = $ci->initConversation();

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance();

        $response = $this->actuators['slack.actuator']->postMessage($next_utterance->getMessage()->getSlackMessage($e));

        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        if ($next_utterance->isCompleting()) {
            var_dump('It completes');
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($ci->getNextUtterance()->getSequence());
        $this->conversation_instance_store->save($ci);
        var_dump('new true');
        return true;
    }

    public function setDefaultMessageInterpreter(InterpreterInterface $interpreter)
    {
        $this->message_interpreter = $interpreter;
    }

    public function setDefaultConversationStore(ConversationTemplateStore $conversation_store)
    {
        $this->conversation_store = $conversation_store;
    }

    public function setDefaultConversationInstanceStore(ConversationInstanceStore $conversation_instance_store)
    {
        $this->conversation_instance_store = $conversation_instance_store;
    }

    private function ongoingConversation(SensorEvent $e)
    {
        //Check if there is an ongoing conversation - instantiate a temp CI object
        //to check against.
        $temp_conversation_instance = new ConversationInstance(null,
            $this->conversation_store,
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId());

        /* @var actsmart\actsmart\Conversations\ConversationInstans $ci */
        return $this->conversation_instance_store->retrieve($temp_conversation_instance);
    }

}