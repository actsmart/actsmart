<?php

namespace actsmart\actsmart\Controllers\Alexa;

use actsmart\actsmart\Controllers\BaseConversationController;
use actsmart\actsmart\Conversations\Utterance;
use actsmart\actsmart\Conversations\WebChat\ConversationInstance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Sensors\Alexa\Events\AlexaMessageEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

class ConversationController extends BaseConversationController
{
    const KEY = 'controller.alexa.conversation_controller';

    /**
     * Implementation of listen function.
     *
     * @param GenericEvent $e
     */
    public function listen(GenericEvent $e)
    {
        if (!$e instanceof AlexaMessageEvent) return;

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

        if (!$nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent)) {
            $this->logger->debug('No next utterance within ongoing conversation');
            return false;
        }

        // Perform actions or information requests associated with the incoming utterance
        $actionResult = $this->performCurrentAction($utterance, $ci);
        $informationResponse = $this->performCurrentInformationRequest($utterance, $ci);

        /**
         * Perform actions or information requests associated with the outgoing utterance.
         * @note This will overwrite previous results right now.
         */
        $nextActionResult = $this->performUtteranceAction($nextUtterance, $utterance);
        $actionResult = $nextActionResult ? $nextActionResult : $actionResult;
        $nextInformationResponse = $this->performUtteranceInformationRequest($nextUtterance, $utterance);
        $informationResponse = $nextInformationResponse ? $nextInformationResponse : $informationResponse;


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

        // Perform actions or information requests associated with the incoming utterance
        $actionResult = $this->performCurrentAction($utterance, $ci);
        $informationResponse = $this->performCurrentInformationRequest($utterance, $ci);

        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        /**
         * Perform actions or information requests associated with the outgoing utterance.
         * @note This will overwrite previous results right now.
         */
        $nextActionResult = $this->performUtteranceAction($nextUtterance, $utterance);
        $actionResult = $nextActionResult ? $nextActionResult : $actionResult;
        $nextInformationResponse = $this->performUtteranceInformationRequest($nextUtterance, $utterance);
        $informationResponse = $nextInformationResponse ? $nextInformationResponse : $informationResponse;

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

        // Perform actions or information requests associated with the incoming utterance
        $actionResult = $this->performCurrentAction($utterance, $ci);
        $informationResponse = $this->performCurrentInformationRequest($utterance, $ci);

        $nextUtterance = $ci->getNextUtterance($this->getAgent(), $utterance, $intent, false);

        /**
         * Perform actions or information requests associated with the outgoing utterance.
         * @note This will overwrite previous results right now.
         */
        $nextActionResult = $this->performUtteranceAction($nextUtterance, $utterance);
        $actionResult = $nextActionResult ? $nextActionResult : $actionResult;
        $nextInformationResponse = $this->performUtteranceInformationRequest($nextUtterance, $utterance);
        $informationResponse = $nextInformationResponse ? $nextInformationResponse : $informationResponse;

        $this->storeContext($nextUtterance, $ci);

        $this->sendMessage($utterance, $nextUtterance, $actionResult, $informationResponse);

        $this->saveConversationInstance($ci, $nextUtterance);

        return true;
    }

    /**
     * Uses the default interpreter to interpret the event
     *
     * @param Map $utterance
     * @return Intent|null
     */
    private function determineEventIntent(Map $utterance)
    {
        $intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);

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
        $arguments->put(Literals::MESSAGE, $nextUtterance->getMessage()->getAlexaResponse($actionResult ?? $utterance, $informationResponse));
        $arguments->put(Literals::USER_ID, $utterance->get(Literals::USER_ID));

        $this->getAgent()->getActuator('actuator.alexa')->perform('action.webchat.postmessage', $arguments);
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
        return [AlexaMessageEvent::KEY];
    }

    /**
     * This performs an action based on the current action set in the ConversationInstance.
     *
     * @param Map $utterance
     * @param ConversationInstance $ci
     * @return mixed|null
     */
    private function performCurrentAction(Map $utterance, $ci)
    {
        $actionResult = null;
        if ($action = $ci->getCurrentAction()) {
            $actionResult = $this->getAgent()->performAction($action, $utterance);
        }
        return $actionResult;
    }

    /**
     * This performs an information request based on the current IR set in the ConversationInstance.
     * @param Map $utterance
     * @param ConversationInstance $ci
     * @return mixed|null
     */
    private function performCurrentInformationRequest(Map $utterance, $ci)
    {
        $informationResponse = null;
        if ($informationRequest = $ci->getCurrentInformationRequest()) {
            $informationResponse = $this->getAgent()->performInformationRequest($informationRequest, $utterance);
        }
        return $informationResponse;
    }

    /**
     * Performes an action
     * @param Utterance $outgoingUtterance
     * @param Map $incomingUtterance
     * @return mixed
     */
    private function performUtteranceAction(Utterance $outgoingUtterance, Map $incomingUtterance)
    {
        $action = $outgoingUtterance->getAction();
        if ($action) {
            return $this->getAgent()->performAction($action, $incomingUtterance);
        }

        return null;
    }

    private function performUtteranceInformationRequest(Utterance $outgoingUtterance, Map $incomingUtterance)
    {
        $informationRequest = $outgoingUtterance->getInformationRequest();
        if ($informationRequest) {
            return $this->getAgent()->performInformationRequest($informationRequest, $incomingUtterance);
        }

        return null;
    }
}
