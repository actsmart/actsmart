<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Conversations\Slack\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackCommandEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackInteractiveMessageEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackMessageActionEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackMessageEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackDialogSubmissionEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\Literals;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;
use Ds\Map;

class ConversationController implements ComponentInterface, ListenerInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait, ListenerTrait;

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     * @return bool
     */
    public function listen(GenericEvent $e)
    {
        if (!($e instanceof SlackEvent)) {
            return false;
        }

        $utterance = $e->getUtterance();

        // @todo Check top level preconditions (bot mentioned, direct message to bot, etc), if none of our top level preconditions match then give up early
        if (!$this->handleOngoingConversation($utterance)) {
            if (!$this->handleNewConversation($utterance)) {
                $this->handleNothingMatched($utterance);
            }
        }
    }

    public function handleOngoingConversation(Map $utterance)
    {
        // If we don't get a CI object back there is no ongoing conversation that matches. Bail out.
        if (!$ci = $this->ongoingConversation($utterance)) {
            $this->logger->debug('Not an ongoing conversation.');
            return false;
        }

        // Set up a default intent
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = $this->determineEventIntent($utterance);

        // Before getting the next utterance let us perform any actions related to the current utterance
        if ($action = $ci->getCurrentAction()) {
            $this->getAgent()->performAction($action, $utterance);
        }

        // If we can't figure out what is the next utterance bail out.
        if (!$next_utterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent)) {
            return false;
        }

        // We have an utterance - let's post the message.
        $response = $this->getAgent()->getActuator['actuator.slack']->perform('action.slack.postmessage', $next_utterance->getMessage()->getSlackResponse($utterance));

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

    public function handleNewConversation(Map $utterance)
    {
        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($utterance);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($utterance, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No matching conversations.');
            return false;
        }

        $workspace_id = $utterance->get(Literals::WORKSPACE_ID);
        $user_id = $utterance->get(Literals::USER_ID);
        $channel_id = $utterance->get(Literals::CHANNEL_ID);
        $timestamp = $utterance->get(Literals::TIMESTAMP);

        $ci = new ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $workspace_id,
            $user_id,
            $channel_id,
            $timestamp,
            $timestamp);

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance.
        // The action result is passed as an argument to a message.
        $action_result = null;
        if ($action = $ci->getCurrentAction()) {
            $arguments = new Map();
            $arguments->put('utterance', $utterance);
            $action_result = $this->getAgent()->performAction($action, $arguments);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $arguments = new Map();
        $arguments->put('message', $next_utterance->getMessage()->getSlackResponse($channel_id, $workspace_id, $action_result ?? $utterance));

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', $arguments);

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
     * @param Map $utterance
     */
    public function handleNothingMatched(Map $utterance)
    {
        $this->logger->debug('Nothing Matched - resorting to default.');

        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $utterance, 1);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($utterance, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $workspace_id = $utterance->get(Literals::WORKSPACE_ID);
        $user_id = $utterance->get(Literals::USER_ID);
        $channel_id = $utterance->get(Literals::CHANNEL_ID);
        $timestamp = $utterance->get(Literals::TIMESTAMP);

        $ci = new ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $workspace_id,
            $user_id,
            $channel_id,
            $timestamp,
            $timestamp);

        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance.
        // The action result is passed as an argument to a message.
        $action_result = null;
        if ($action = $ci->getCurrentAction()) {
            $arguments = new Map();
            $arguments->put('utterance', $utterance);
            $action_result = $this->getAgent()->performAction($action, $arguments);
        }

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $arguments = new Map();
        $arguments->put('message', $next_utterance->getMessage()->getSlackResponse($channel_id, $workspace_id, $action_result ?? $utterance));

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', $arguments);

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

    private function ongoingConversation(Map $utterance)
    {
        $workspace_id = $utterance->get(Literals::WORKSPACE_ID);
        $user_id = $utterance->get(Literals::USER_ID);
        $channel_id = $utterance->get(Literals::CHANNEL_ID);

        //Check if there is an ongoing conversation - instantiate a temp CI object
        //to check against.
        $temp_conversation_instance = new ConversationInstance(null,
            $this->agent->getStore('store.conversation_templates'),
            $workspace_id,
            $user_id,
            $channel_id);

        // Attempt to retrieve a conversation store
        $conversation_instance_store = $this->agent->getStore('store.conversation_instance');
        return $conversation_instance_store->retrieve($temp_conversation_instance);
    }

    /**
     * Builds an apporpriate Intent object based on the event that should generate the Intent.
     *
     * @param Map $utterance
     * @return Intent|null
     */
    private function determineEventIntent(Map $utterance)
    {
        $intent = null;
        switch ($utterance->get(Literals::TYPE)) {
            case Literals::SLACK_INTERACTIVE_MESSAGE:
                $intent = new Intent($utterance->get(Literals::CALLBACK_ID), $utterance, 1);
                break;
            case Literals::SLACK_MESSAGE:
                $intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_COMMAND:
                $intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_DIALOG_SUBMISSION:
                $intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_MESSAGE_ACTION:
                $intent = new Intent($utterance->get(Literals::CALLBACK_ID), $utterance, 1);
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
        return ['event.slack.message', 'event.slack.interactive_message', 'event.slack.command', 'event.slack.dialog_submission', 'event.slack.messageaction'];
    }

}
