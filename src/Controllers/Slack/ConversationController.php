<?php

namespace actsmart\actsmart\Controllers\Slack;


use actsmart\actsmart\Interpreters\InterpreterInterface;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackEvent;
use actsmart\actsmart\Stores\ConversationTemplateStore;
use actsmart\actsmart\Stores\ConversationInstanceStore;
use actsmart\actsmart\Conversations\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;

class ConversationController implements ComponentInterface, ListenerInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait;

    const SLACK_ACTION_TYPE_BUTTON = 'button';
    const SLACK_ACTION_TYPE_MENU = 'menu';

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     * @return bool
     */
    public function listen(GenericEvent $e)
    {
        if (!($e instanceOf SlackEvent)) {
            return false;
        }

        // @todo Check top level preconditions (bot mentioned, direct message to bot, etc), if none of our top level preconditions match then give up early
        if (!$this->handleOngoingConversation($e)) {
            if (!$this->handleNewConversation($e)) {
                $this->handleNothingMatched($e);
            }
        }
    }

    public function handleOngoingConversation(SlackEvent $e)
    {
        // If we don't get a CI object back there is no ongoing conversation that matches. Bail out.
        if (!$ci=$this->ongoingConversation($e)) {
            return false;
        }

        // Set up a default intent
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $default_intent = $this->message_interpreter->interpret($e);

        // Before getting the next utterance let us perform any actions related to the current utterance
        $ci->performCurrentAction($e);

        // If we can't figure out what is the next utterance bail out.
        if (!$next_utterance = $ci->getNextUtterance($e, $default_intent)) {
            return false;
        }

        // We have an utterance - let's post the message.
        $response = $this->actuators['slack.actuator']->postMessage($next_utterance->getMessage()->getSlackMessage($e));

        // @todo if an ongoing conversation finishes we have to get rid of the record on Dynamo!
        if ($next_utterance->isCompleting()) {
            // Remove the current ci state.
            $this->conversation_instance_store->delete($ci);
            return true;
        }

        // Remove the current ci state
        // @todo - this can be done with an update as well potentially.
        $this->conversation_instance_store->delete($ci);

        // If the utterance we just sent does not end the conversation, store the CI instance.
        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);
        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->conversation_instance_store->save($ci);
        return true;
    }

    public function handleNewConversation(SensorEvent $e)
    {
        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($e);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            return false;
        }

        $ci = new ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelid(),
            $e->getTimestamp(),
            $e->getTimestamp());

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance
        // !!!!!Get the current utterance, derive the action name and call on the agent to perform the action.
        if ($action = $ci->getCurrentAction()) {
            $this->getAgent()->performAction($action, $e);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', $next_utterance->getMessage()->getSlackMessage($e));

        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->conversation_instance_store->save($ci);
        return true;
    }

    /**
     * @param GenericEvent $e
     */
    public function handleNothingMatched($e)
    {
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $e, 1);

        $matching_conversation_id = $this->conversation_store->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            return false;
        }

        $ci = new ConversationInstance($matching_conversation_id,
            $this->conversation_store,
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId(),
            $e->getTimestamp(),
            $e->getTimestamp());

        $ci->initConversation();

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($e, $intent, false);

        $response = $this->actuators['slack.actuator']->postMessage($next_utterance->getMessage()->getSlackMessage($e));

        dd($response);

        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($ci->getNextUtterance()->getSequence());
        $this->conversation_instance_store->save($ci);
        return true;
    }

    private function ongoingConversation(SensorEvent $e)
    {
        //Check if there is an ongoing conversation - instantiate a temp CI object
        //to check against.
        $temp_conversation_instance = new ConversationInstance(null,
            $this->agent->getStore('store.conversation_templates'),
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId());

        // Attempt to retrieve a conversation store
        $conversation_instance_store = $this->agent->getStore('store.conversation_instance');
        return $conversation_instance_store->retrieve($temp_conversation_instance);
    }

    /**
     * Builds an apporpriate Intent object based on the event that should generate the Intent.
     *
     * @param SensorEvent $e
     * @return Intent|null
     */
    private function determineEventIntent(SensorEvent $e)
    {
        $intent = null;
        switch ($e->getSubject()) {
            case 'interactive_message':
                $intent = new Intent($e->getCallbackId(), $e, 1);
                break;
            case 'message':
                $intent = $this->getAgent()->getInterpreter('interpreter.luis')->interpret($e);
                break;
            default:
                $intent = new Intent();
        }

        return $intent;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'controller.slack.conversation_controller';
    }

    public function listensForEvents()
    {
        return ['event.slack.message', 'event.slack.interactive_message'];
    }
}
