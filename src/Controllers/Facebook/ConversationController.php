<?php

namespace actsmart\actsmart\Controllers\Facebook;

use actsmart\actsmart\Conversations\Facebook;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\Facebook\Events\FacebookEvent;
use actsmart\actsmart\Sensors\Facebook\Events\FacebookMessageEvent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use Illuminate\Support\Facades\Log;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;

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
        if (!($e instanceof FacebookEvent)) {
            return false;
        }

        // @todo Check top level preconditions (bot mentioned, direct message to bot, etc), if none of our top level preconditions match then give up early
        if (!$this->handleOngoingConversation($e)) {
            if (!$this->handleNewConversation($e)) {
                $this->handleNothingMatched($e);
            }
        }
    }

    public function handleOngoingConversation(GenericEvent $e)
    {
        //@todo
        return false;
    }

    public function handleNewConversation(FacebookMessageEvent $e)
    {
        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($e);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No matching conversations.');
            return false;
        }

        $ci = new Facebook\ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
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

        $response = $this->getAgent()->getActuator('actuator.facebook')->perform('action.facebook.postmessage', [
            'message' => $next_utterance->getMessage()->getFacebookResponse($e->getSenderId(), $action_result ?? $e)
        ]);

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back and provide a fallback..
        $ts = $response->ts ?? $response->message_ts ?? time();
        $ci->setUpdateTs((int)explode('.', $ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($next_utterance->getSequence());

        // Not saving conversations for now
        //$this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    /**
     * @param GenericEvent $e
     * @return bool
     */
    public function handleNothingMatched(GenericEvent $e)
    {
        $this->logger->debug('Nothing Matched - resorting to default.');

        /* @var \actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $e, 1);

        $matching_conversation_id = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matching_conversation_id) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $ci = new Facebook\ConversationInstance($matching_conversation_id,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getTimestamp(),
            $e->getTimestamp());

        $ci->initConversation();

        // Before getting the next utterance let us perform any actions related to the current utterance.
        // The action result is passed as an argument to a message.
        $action_result = null;
        if ($action = $ci->getCurrentAction()) {
            $action_result = $this->getAgent()->performAction($action, ['event' => $e]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $next_utterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $response = $this->getAgent()->getActuator('actuator.slack')->perform('action.facebook.postmessage', [
            'message' => $next_utterance->getMessage()->getSlackResponse($e->getChannelId(), $e->getWorkspaceId(), $action_result ?? $e)
        ]);

        // @todo Improve this - we are trying to handle two different ways of sending timestamps back and provide a fallback..
        $ts = $response->ts ?? $response->message_ts ?? time();
        $ci->setUpdateTs((int)explode('.', $ts)[0]);

        if ($next_utterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($ci->getNextUtterance()->getSequence());
        // Not saving instances for Facebook for now.
        //$this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    private function ongoingConversation(SensorEvent $e)
    {
        //Check if there is an ongoing conversation - instantiate a temp CI object
        //to check against.
        return false;
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
            case $e instanceof FacebookMessageEvent:
                $intent = new Intent($e->getText(), $e, 1);
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
        return 'controller.facebook.conversation_controller';
    }

    public function listensForEvents()
    {
        return ['event.facebook.message'];
    }

}
