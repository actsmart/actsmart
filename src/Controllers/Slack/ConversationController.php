<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackInteractiveMessageEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackMessageEvent;
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
            $this->logger->debug('Not an ongoing conversation.');
            return false;
        }

        // Set up a default intent
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = $this->determineEventIntent($e);

        // Before getting the next utterance let us perform any actions related to the current utterance
        if ($action = $ci->getCurrentAction()) {
            $this->getAgent()->performAction($action, $e);
        }

        // If we can't figure out what is the next utterance bail out.
        if (!$next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent)) {
            return false;
        }

        // We have an utterance - let's post the message.
        $response = $this->getAgent()->getActuator['actuator.slack']->perform('action.slack.postmessage', $next_utterance->getMessage()->getSlackResponse($e));

        // @todo if an ongoing conversation finishes we have to get rid of the record on Dynamo!
        if ($next_utterance->isCompleting()) {
            // Remove the current ci state.
            $this->getAgent()->getStore('store.conversation_instance')->delete($ci);
            return true;
        }

        // Remove the current ci state
        // @todo - this can be done with an update as well potentially.
        $this->getAgent()->getStore('store.conversation_instance')->delete($ci);

        // If the utterance we just sent does not end the conversation, store the CI instance.
        $ci->setUpdateTs((int)explode('.', $response->ts)[0]);
        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    public function handleNewConversation(GenericEvent $e)
    {
        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($e);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No matching conversations.');
            return false;
        }

        $ci = new ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId(),
            $e->getTimestamp(),
            $e->getTimestamp());

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance.
        // The action result is passed as an argument to a message.
        $action_result = null;
        if ($action = $ci->getCurrentAction()) {
            $action_result = $this->getAgent()->performAction($action, ['event' => $e]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', [
            'message' => $next_utterance->getMessage()->getSlackResponse($e->getChannelId(), $e->getWorkspaceId(), $action_result ?? $e)
        ]);

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back and provide a fallback..
        $ts = $response->ts ?? $response->message_ts ?? time();
        $ci->setUpdateTs((int)explode('.', $ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());
        $this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    /**
     * @param GenericEvent $e
     */
    public function handleNothingMatched(GenericEvent $e)
    {
        $this->logger->debug('Nothing Matched - resorting to default.');

        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $e, 1);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $ci = new ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getWorkspaceId(),
            $e->getUserId(),
            $e->getChannelId(),
            $e->getTimestamp(),
            $e->getTimestamp());

        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance.
        // The action result is passed as an argument to a message.
        $action_result = null;
        if ($action = $ci->getCurrentAction()) {
            $action_result = $this->getAgent()->performAction($action, ['event' => $e]);
        }

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', [
            'message' => $next_utterance->getMessage()->getSlackResponse($e->getChannelId(), $e->getWorkspaceId(), $action_result ?? $e)
        ]);

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back and provide a fallback..
        $ts = $response->ts ?? $response->message_ts ?? time();
        $ci->setUpdateTs((int)explode('.', $ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($ci->getNextUtterance()->getSequence());
        $this->getAgent()->getStore('store.conversation_instance')->save($ci);
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
        switch (true) {
            case $e instanceof SlackInteractiveMessageEvent:
                $intent = new Intent($e->getCallbackId(), $e, 1);
                break;
            case $e instanceof SlackMessageEvent:
                $intent = $this->getAgent()->getDefaultConversationInterpreter()->interpret($e);
                break;
            default:
                $intent = new Intent();
        }

        $this->logger->debug('Created an intent', (array)$intent);
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
