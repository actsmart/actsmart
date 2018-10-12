<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\WebChat\WebChatActuator;
use actsmart\actsmart\Controllers\WebChat\ConversationController;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatEventCreator;
use actsmart\actsmart\Sensors\WebChat\WebChatSensor;
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
     * Setup the generic components required for a WebChatAgent.
     */
    private function configure()
    {
        $contextStore = new ContextStore();
        $this->addComponent($contextStore);

        // We need to pickup WebChat events so need to add a WebChat sensor
        $this->addComponent(new WebChatSensor(new WebChatEventCreator()));

        // A more complex controller that handles conversations.
        $this->addComponent(new ConversationController());

        // A simple key:value context store to share state.
        $this->addComponent(new ContextStore());

        // The WebChat actuator that sends messages to the WebChat component.
        $this->addComponent(new WebChatActuator());
    }
}
