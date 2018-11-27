<?php

namespace actsmart\actsmart\Controllers\WebChat;

use actsmart\actsmart\Controllers\BaseConversationController;
use actsmart\actsmart\Conversations\Utterance;
use actsmart\actsmart\Conversations\WebChat\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatUtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

class ConversationController extends BaseConversationController
{
    const KEY = 'controller.webchat.conversation_controller';

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     */
    public function listen(GenericEvent $e)
    {
        if (!$e instanceof WebChatUtteranceEvent) return;

        $utterance = $e->getUtterance();
        $intent = $this->determineEventIntent($utterance);

        if (!$this->handleOngoingConversation($utterance, $intent)) {
            if (!$this->handleNewConversation($utterance, $intent)) {
                $this->logger->info('Nothing Matched - resorting to default.');
                $this->handleNothingMatched($utterance);
            }
        }
    }

    /**
     * Logic for ongoing conversations. If there is no ongoing conversation for the user, or there is no further utterance
     * on the current scene returns false.
     *
     * Otherwise, sends the message and saves the current instance in the conversation instance store
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return bool
     */
    public function handleOngoingConversation(Map $utterance, Intent $intent)
    {
        if (!$ci = $this->ongoingConversation($utterance)) {
            $this->logger->debug('Not an ongoing conversation');
            return false;
        }

        $actionResult = $this->performAction($utterance, $ci);

        $informationResponse = $this->performInformationRequest($utterance, $ci);

        if (!$nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent)) {
            $this->logger->debug('No next utterance within ongoing conversation');
            return false;
        }

        $this->storeContext($nextUtterance, $ci);

        $this->sendMessage($utterance, $nextUtterance, $actionResult, $informationResponse);

        $this->saveConversationInstance($ci, $nextUtterance);

        return true;
    }

    /**
     * Logic for new conversations. If there is no conversation matching the intent, returns false.
     * Otherwise creates a new conversation instance, sends the message and updates the conversation instance store
     *
     * @param Map $utterance
     * @param Intent $intent
     * @return bool
     */
    public function handleNewConversation(Map $utterance, Intent $intent)
    {
        $matchingConversation = $this->getMatchingConversation($utterance, $intent);
        if (!$matchingConversation) {
            return false;
        }

        $ci = $this->createConversationInstance($utterance, $matchingConversation->getId());

        $actionResult = $this->performAction($utterance, $ci);

        $informationResponse = $this->performInformationRequest($utterance, $ci);

        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $this->storeContext($nextUtterance, $ci);

        $this->sendMessage($utterance, $nextUtterance, $actionResult, $informationResponse);

        $this->saveConversationInstance($ci, $nextUtterance);

        return true;
    }

    /**
     * @param Map $utterance
     * @return bool
     */
    public function handleNothingMatched(Map $utterance)
    {
        $intent = $this->noMatchIntent($utterance);

        $matchingConversation = $this->getMatchingConversation($utterance, $intent);
        if (!$matchingConversation) {
            $this->logger->debug('No support for NoMatch conversation.');
            return false;
        }

        $ci = $this->createConversationInstance($utterance, $matchingConversation->getId());

        $actionResult = $this->performAction($utterance, $ci);

        $informationResponse = $this->performInformationRequest($utterance, $ci);

        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        $this->storeContext($nextUtterance, $ci);

        $this->sendMessage($utterance, $nextUtterance, $actionResult, $informationResponse);

        $this->saveConversationInstance($ci, $nextUtterance);

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

    /**
     * @param Map $utterance
     * @param $nextUtterance Utterance
     * @param $actionResult
     * @param $informationResponse
     */
    private function sendMessage(Map $utterance, $nextUtterance, $actionResult, $informationResponse): void
    {
        $arguments = new Map();
        $arguments->put(Literals::MESSAGE, $nextUtterance->getMessage()->getWebChatResponse($actionResult ?? $utterance, $informationResponse));
        $arguments->put(Literals::USER_ID, $utterance->get(Literals::USER_ID));

        $this->getAgent()->getActuator('actuator.webchat')->perform('action.webchat.postmessage', $arguments);
    }

    /**
     * @param Map $utterance
     * @param $matchingConversationId
     * @return ConversationInstance
     */
    private function createConversationInstance(Map $utterance, $matchingConversationId): ConversationInstance
    {
        $ci = new ConversationInstance (
            $matchingConversationId,
            $this->getAgent()->getStore('store.conversation_templates'),
            $utterance->get(Literals::USER_ID),
            new \DateTime());

        /* @var \actsmart\actsmart\Conversations\Conversation $conversation */
        $ci->initConversation();
        return $ci;
    }

    public function getKey()
    {
        return self::KEY;
    }

    public function listensForEvents()
    {
        return [WebChatUtteranceEvent::KEY];
    }

    /**
     * @param Map $utterance
     * @param ConversationInstance $ci
     * @return mixed|null
     */
    private function performAction(Map $utterance, $ci)
    {
        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, $utterance);
        }
        return $actionResult;
    }

    /**
     * @param Map $utterance
     * @param ConversationInstance $ci
     * @return mixed|null
     */
    private function performInformationRequest(Map $utterance, $ci)
    {
        $informationResponse = null;
        if ($informationRequest = $ci->getCurrentInformationRequest()) {
            $informationResponse = $this->getAgent()->performInformationRequest($informationRequest, $utterance);
        }
        return $informationResponse;
    }
}
