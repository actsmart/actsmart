<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\WebChat\WebChatActuator;
use actsmart\actsmart\Controllers\WebChat\ConversationController;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatEventCreator;
use actsmart\actsmart\Sensors\WebChat\WebChatSensor;
use actsmart\actsmart\Stores\ConfigStore;
use actsmart\actsmart\Stores\ContextStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class WebChatAgent extends Agent
{
    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger)
    {
        parent::__construct($dispatcher, $logger);

        $this->configure();
    }

    /**
     * Setup the generic components required for a SlackAgent.
     */
    private function configure()
    {
        $config_store = new ConfigStore();
        $this->addComponent($config_store);

        // We need to pickup Slack events so need to add a Slack sensor
        $this->addComponent(new WebChatSensor(new WebChatEventCreator()));

        // A more complex controller that handles conversations.
        $this->addComponent(new ConversationController());

        // A simple key:value context store to share state.
        $this->addComponent(new ContextStore());

        // The Slack actuator that sends messages to Slack.
        $this->addComponent(new WebChatActuator());
    }
}
