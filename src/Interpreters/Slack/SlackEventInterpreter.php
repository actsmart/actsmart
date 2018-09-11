<?php
namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\BaseIntentInterpreter;
use actsmart\actsmart\Interpreters\Intent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use Symfony\Component\EventDispatcher\GenericEvent;
use Ds\Map;

class SlackEventInterpreter extends BaseIntentInterpreter implements ListenerInterface
{
    use ListenerTrait;

    const SLACK_EVENT_INTERPRETER_PRIORITY = 255;

    public function __construct()
    {
        $this->setPriority($this::SLACK_EVENT_INTERPRETER_PRIORITY);
    }

    public function listen(GenericEvent $e)
    {
        if (!$e instanceof UtteranceEvent) return null;

        $utterance = $e->getUtterance();

        $this->interpretUtterance($utterance);
    }

    public function interpretUtterance(Map $utterance) : Intent
    {

    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'interpreter.slack.event';
    }

    public function listensForEvents()
    {
        return ['event.slack.message', 'event.slack.interactive_message'];
    }
}