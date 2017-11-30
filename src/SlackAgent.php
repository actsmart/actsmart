<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\Slack\SlackActuator;
use actsmart\actsmart\Stores\ConfigStore;
use actsmart\actsmart\Sensors\Slack\SlackSensor;
use actsmart\actsmart\Sensors\Slack\Events\SlackEventCreator;
use actsmart\actsmart\Controllers\Slack\ConversationController;
use actsmart\actsmart\Controllers\Slack\URLVerificationController;
use actsmart\actsmart\Stores\ContextStore;
use actsmart\actsmart\Stores\Slack\SlackConfigStore;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Log\LoggerInterface;

class SlackAgent extends Agent
{
    private $slack_verification_token;

    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger, $slack_verification_token)
    {
        parent::__construct($dispatcher, $logger);

        $this->slack_verification_token = $slack_verification_token;

        $this->configureForSlack();
    }

    /**
     * Setup the generic components required for a SlackAgent.
     */
    private function configureForSlack()
    {
        // We use a store to keep external config
        $config_store = new SlackConfigStore();
        $config_store->add('slack', 'app.token', $this->slack_verification_token);
        $this->addComponent($config_store);

        // We need to pickup Slack events so need to add a Slack sensor
        $this->addComponent(new SlackSensor(new SlackEventCreator()));

        // A simple controller for URL Verification.
        $this->addComponent(new URLVerificationController());

        // A more complex controller that handles conversations.
        $this->addComponent(new ConversationController());

        // A simple key:value context store to share state.
        $this->addComponent(new ContextStore());

        // The Slack actuator that sends messages to Slack.
        $this->addComponent(new SlackActuator());

    }

}
