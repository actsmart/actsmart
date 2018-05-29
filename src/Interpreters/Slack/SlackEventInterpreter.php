<?php
namespace actsmart\actsmart\Interpreters\Slack;

use actsmart\actsmart\Interpreters\BaseInterpreter;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use Symfony\Component\EventDispatcher\GenericEvent;

class SlackEventInterpreter extends BaseInterpreter implements ListenerInterface
{
    use ListenerTrait;

    const SLACK_EVENT_INTERPRETER_PRIORITY = 255;

    public function __construct()
    {
        $this->setPriority($this::SLACK_EVENT_INTERPRETER_PRIORITY);
    }

    public function listen(GenericEvent $e)
    {
        $this->interpret($e);
    }

    public function interpret(GenericEvent $e)
    {
        // @todo gather relevant info from events and add to ContextStore
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