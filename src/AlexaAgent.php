<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\Alexa\AlexaActuator;
use actsmart\actsmart\Controllers\Alexa\ConversationController;
use actsmart\actsmart\Sensors\Alexa\AlexaSensor;
use actsmart\actsmart\Sensors\Alexa\Events\AlexaEventCreator;
use actsmart\actsmart\Stores\ContextStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class AlexaAgent extends Agent
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

        $this->addComponent(new AlexaSensor(new AlexaEventCreator()));

        $this->addComponent(new ConversationController());

        // A simple key:value context store to share state.
        $this->addComponent(new ContextStore());

        // Actuator to send the message back to Alexa
        $this->addComponent(new AlexaActuator());
    }
}
