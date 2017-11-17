<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackInteractiveMessageEvent;
use actsmart\actsmart\Sensors\Slack\Events\SlackMessageEvent;
use actsmart\actsmart\Conversations\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Stores\ConfigurationStoreValueNotSetException;
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

        // Make sure we have the tokens we need to communicate with the bot
        if (!$this->checkForTokens($e)) {
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
        $default_intent = $this->getAgent()->getInterpreter('interpreter.luis')->interpret($e);

        // Before getting the next utterance let us perform any actions related to the current utterance
        if ($action = $ci->getCurrentAction()) {
            $this->getAgent()->performAction($action, $e);
        }

        // If we can't figure out what is the next utterance bail out.
        if (!$next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $default_intent)) {
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
        if ($action = $ci->getCurrentAction()) {
            $this->getAgent()->performAction($action, $e);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.slack.postmessage', $next_utterance->getMessage()->getSlackResponse($e));

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back.

        $ts = isset($response->ts) ? $response->ts : $response->message_ts;
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
        /* @var actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $e, 1);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
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

        /* @var actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('actuator.slack.postmessage', $next_utterance->getMessage()->getSlackResponse($e));

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back.
        $ts = isset($response->ts) ? $response->ts : $response->message_ts;
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
                $intent = $this->getAgent()->getInterpreter('interpreter.luis')->interpret($e);
                break;
            default:
                $intent = new Intent();
        }

        $this->logger->debug('Created an intent', (array)$intent);
        return $intent;
    }

    /**
     * Checks if store.config has the token required and if not uses
     * an actuator to retrieve it. Users of actSmart need to implement
     * that actuator or set the token earlier in the store so the actuator
     * is not required.
     *
     * @param GenericEvent $e
     * @return bool
     */
    private function checkForTokens(GenericEvent $e)
    {
        // Tokens should be in the config store.
        $config_store = $this->getAgent()->getStore('store.config');
        $token = false;

        // Try to retrieve the token and if not available invoke the actuator
        // to populate the store with it.
        try {
            $token = $config_store->get('slack.oauth_token');
        } catch(ConfigurationStoreValueNotSetException $exception) {
            $this->getAgent()->performAction('action.slack.retrieve_oauth_token', $e);
            $token = $config_store->get('oauth_token.slack');
        }
        return $token;
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
