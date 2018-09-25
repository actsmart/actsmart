<?php

namespace actsmart\actsmart\Controllers\WebChat;

use actsmart\actsmart\Conversations\WebChat\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ActionEvent;
use actsmart\actsmart\Sensors\WebChat\Events\MessageEvent;
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

    const KEY = 'controller.webchat.conversation_controller';

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     * @return bool
     */
    public function listen(GenericEvent $e)
    {
        if (!$e instanceof UtteranceEvent) return null;

        $utterance = $e->getUtterance();

        if (!$this->handleNewConversation($utterance)) {
            $this->handleNothingMatched($utterance);
        }
    }

    public function handleNewConversation(Map $utterance)
    {

        /* @var \actsmart\actsmart\Interpreters\intent $intent */
        $intent = $this->determineEventIntent($utterance);

        $matchingConversationId = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($utterance, $intent);

        if (!$matchingConversationId) {
            $this->logger->debug('No matching conversations.');
            return false;
        }

        $ci = new ConversationInstance(
            $matchingConversationId,
            $this->getAgent()->getStore('store.conversation_templates'),
            $utterance->get(Literals::UID));

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();

        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, [Literals::UTTERANCE => $utterance]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $nextUtterance */
        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $this->getAgent()->getActuator('actuator.webchat')->perform('action.webchat.postmessage', [
            Literals::MESSAGE => $nextUtterance->getMessage()->getWebChatResponse($actionResult ?? $utterance)
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
    public function handleNothingMatched(Map $utterance)
    {
        $this->logger->debug('Nothing Matched - resorting to default.');

        /* @var \actsmart\actsmart\Interpreters\Intent $intent */
        $intent = new Intent('NoMatch', $utterance, 1);

        $matchingConversationId = $this->getAgent()->getStore('store.conversation_templates')->getMatchingConversation($utterance, $intent);

        if (!$matchingConversationId) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $ci = new ConversationInstance(
            $matchingConversationId,
            $this->getAgent()->getStore('store.conversation_templates'),
            $utterance->get(Literals::UID));

        $ci->initConversation();

        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, ['event' => $utterance->get(Literals::SOURCE_EVENT)]);
        }

        /* @var \actsmart\actsmart\Conversations\Utterance $next_utterance */
        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $this->getAgent()->getActuator('actuator.webchat')->perform('action.webchat.postmessage', [
            Literals::MESSAGE => $nextUtterance->getMessage()->getWebChatResponse($actionResult ?? $utterance)
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
     * @param Map $utterance
     * @return Intent|null
     */
    private function determineEventIntent(Map $utterance)
    {
        $intent = null;
        switch ($utterance->get(Literals::TYPE)) {
            case Literals::WEB_CHAT_MESSAGE:
                $intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::WEB_CHAT_ACTION:
                $intent = new Intent($utterance->get(Literals::CALLBACK_ID));
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
        return [ActionEvent::EVENT_NAME, MessageEvent::EVENT_NAME];
    }
}
