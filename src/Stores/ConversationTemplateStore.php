<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\Utterance;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

abstract class ConversationTemplateStore implements ConversationTemplateStoreInterface, ComponentInterface, StoreInterface
{
    use ComponentTrait;

    protected $conversations = [];

    private $default_intent = null;

    /**
     * @param Conversation $conversation
     */
    public function addConversation(Conversation $conversation)
    {
        $this->conversations[$conversation->getConversationTemplateId()] = $conversation;
    }

    /**
     * @param array $conversations
     */
    public function addConversations($conversations)
    {
        $this->conversations = $conversations;
    }

    public function getConversation($conversation_template_id)
    {
        return $this->conversations[$conversation_template_id];
    }

    /**
     * Returns the set of conversations whose opening utterance has an intent that
     * matches the $intent.
     *
     * @param Map $utterance
     * @return array | boolean
     */
    public function getMatchingConversations(Map $utterance)
    {
        $matches = [];
        foreach ($this->conversations as $conversation) {
                $scene = $conversation->getInitialScene();

            // Check preconditions and if good then check interpreter
            if ($this->getAgent()->checkIntentConditions($scene->getPreconditions(), $utterance)) {
                /** @var Utterance $u */
                $u = $conversation->getInitialScene()->getInitialUtterance();

                if ($u->hasIntentInterpreter()) {
                    $conversationIntent = $this->getAgent()->interpretIntent($u->getIntentInterpreter(), $utterance);
                } else {
                    $conversationIntent = $this->determineEventIntent($utterance);
                }

                if ($u->intentMatches($conversationIntent)) {
                    $matches[$conversation->getConversationTemplateId()] = $conversation;
                }
            }
        }

        if (count($matches) > 0) {
            return array_keys($matches);
        }

        return false;
    }

    /**
     * Returns a single match - the first conversation that matchs for now.
     *
     * @todo This should become more sophisticated than simply return the first
     * conversation.
     *
     * @param Map $utterance
     * @return mixed
     */
    public function getMatchingConversation(Map $utterance)
    {
        $matches = $this->getMatchingConversations($utterance);

        if (!$matches) {
            return false;
        }

        // Have to do below to avoid a PHP_STRICT error for variables passed by reference
        // when operation happens in a single pass.
        $reversed_matches = array_reverse($matches);
        $match = array_pop($reversed_matches);

        return $match;
    }

    /**
     * Builds an apporpriate Intent object based on the event that should generate the Intent.
     *
     * @param Map $utterance
     * @return Intent|null
     */
    private function determineEventIntent(Map $utterance)
    {
        if ($this->default_intent) {
            return $this->default_intent;
        }

        switch ($utterance->get(Literals::TYPE)) {
            case Literals::SLACK_INTERACTIVE_MESSAGE:
                $this->default_intent = new Intent($utterance->get(Literals::CALLBACK_ID), $utterance, 1);
                break;
            case Literals::SLACK_MESSAGE:
                $this->default_intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_COMMAND:
                $this->default_intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_DIALOG_SUBMISSION:
                $this->default_intent = $this->getAgent()->getDefaultIntentInterpreter()->interpretUtterance($utterance);
                break;
            case Literals::SLACK_MESSAGE_ACTION:
                $this->default_intent = new Intent($utterance->get(Literals::CALLBACK_ID), $utterance, 1);
                break;
            default:
                $this->default_intent = new Intent();
        }

        return $this->default_intent;
    }
}
