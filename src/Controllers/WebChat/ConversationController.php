<?php

namespace actsmart\actsmart\Controllers\WebChat;

use actsmart\actsmart\Conversations\WebChat\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\WebChat\Events\MessageEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;

class ConversationController implements ComponentInterface, ListenerInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait, ListenerTrait;

    const KEY = 'controller.webchat.conversation_controller';

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     * @return bool
     */
    public function listen(GenericEvent $e)
    {
        if (!($e instanceof MessageEvent)) {
            return false;
        }

        if (!$this->handleNewConversation($e)) {
            $this->handleNothingMatched($e);
        }
    }

    public function handleNewConversation(MessageEvent $e)
    {
        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($e);

        $matchingConversationId = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matchingConversationId) {
            $this->logger->debug('No matching conversations.');
            return false;
        }

        $ci = new ConversationInstance(
            $matchingConversationId,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getUserId());

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();

        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, ['event' => $e]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $nextUtterance */
        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $this->getAgent()->getActuator('actuator.webchat')->perform('action.webchat.postmessage', [
            'message' => $nextUtterance->getMessage()->getWebChatResponse($actionResult ?? $e)
        ]);

        if ($nextUtterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($nextUtterance->getSequence());
        $this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    /**
     * @param MessageEvent $e
     * @return bool
     */
    public function handleNothingMatched(MessageEvent $e)
    {
        $this->logger->debug('Nothing Matched - resorting to default.');

        /* @var \actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $e, 1);

        $matchingConversationId = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($e, $intent);

        if (!$matchingConversationId) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $ci = new ConversationInstance(
            $matchingConversationId,
            $this->getAgent()->getStore('store.conversation_templates'),
            $e->getUserId());

        $ci->initConversation();

        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, ['event' => $e]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $e, $intent, false);

        $this->getAgent()->getActuator('actuator.webchat')->perform('action.webchat.postmessage', [
            'message' => $nextUtterance->getMessage()->getWebChatResponse($actionResult ?? $e)
        ]);

        if ($nextUtterance->isCompleting()) {
            return true;
        }

        $ci->setCurrentUtteranceSequenceId($ci->getNextUtterance()->getSequence());
        $this->getAgent()->getStore('store.conversation_instance')->save($ci);
        return true;
    }

    /**
     * Returns the intent for the message. At this stage we are just supporting @see MessageEvents
     *
     * @param SensorEvent $e
     * @return Intent|null
     */
    private function determineEventIntent(SensorEvent $e)
    {
        $intent = null;
        switch (true) {
            case $e instanceof MessageEvent:
                $intent = $this->getAgent()->getDefaultConversationInterpreter()->interpret($e);
                break;
            default:
                $intent = new Intent();
        }

        $this->logger->debug('Created an intent', (array)$intent);
        return $intent;
    }

    public function getKey()
    {
        return self::KEY;
    }

    public function listensForEvents()
    {
        return [MessageEvent::EVENT_NAME];
    }
}
