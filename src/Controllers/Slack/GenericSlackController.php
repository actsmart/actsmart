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
        //Check if there is an ongoing conversation - instantiate a temp CI object
        //to check against.
        $temp_conversation_instance = new ConversationInstance(null,
            $this->conversation_store,
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId());

        /* @var actsmart\actsmart\Conversations\ConversationInstans $ci */
        $ci = $this->conversation_instance_store->retrieve($temp_conversation_instance);

        // If we don't get a CI object back there is no ongoing conversation that matches. Bail out.
        if (!$ci) return false;

        // We do have a conversation template id so let's thaw the actual conversation graph. We only
        // have the id before this point.
        $ci->setConversation();

        // Get the current scene and possible follow ups.
        $current_scene = $ci->getConversation()->getScene($ci->getCurrentSceneId());
        $possible_followups = $current_scene->getPossibleFollowUps($ci->getCurrentUtteranceSequenceId());

        // Determine the intent of the message that was sent
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = $this->message_interpreter->interpret($e);

        // Check each possible followup for a match
        $matching_followups = [];

        foreach ($possible_followups as $followup) {
            if ($followup->hasInterpreter()) {
                if ($followup->intentMatches($followup->interpret($e))) {$matching_followups[] = $followup;}
            } else {
                if ($followup->intentMatches($intent)) {$matching_followups[] = $followup;}
            }
        }

        // We couldn't find any matching intent. Get out.
        if (count($matching_followups) == 0) return false;

        // At this point we definitely have a matching intent so let us post the corresponsing message.
        // Keeping it simple - just the first matching utterance. We are keeping it simple - just the first
        // matching utterance.
        $current_utterance = $matching_followups[0];
        $next_utterance = null;

        // The response will be the appropriate response for the message that matched.

        // There are two possibilities.
        // 1. We are in the same scene so we get the next message in that scene. Let's check.
        if (!$current_utterance->changesScene()) {
            $next_utterance = $ci->getNextUtterace();
        } else {
            // We *are* changing scenes! Set the current scene as the new scene.
            $ci->setCurrentSceneId($current_utterance->getEndScene());

            // Given the new scene the next message is going to be the first message of the scene that
            // has a sequence higher than the current message and replies to the current message sender
            // @todo - we have to improve get nextUtterance to be more generic than just the message with the next
            // sequence id.
            $ci->getNextUtterance();
        }

        $response = $this->actuators['slack.actuator']->postMessage($matching_followups[0]->getMessage()->getSlackMessage($e));


        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        // @todo if an ongoing conversation finishes we have to get rid of the record on Dynamo!
        if ($matching_followups[0]->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($matching_followups[0]->getSequence());
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
}