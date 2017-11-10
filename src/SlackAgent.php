<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Stores\ConfigStore;
use actsmart\actsmart\Sensors\Slack\SlackSensor;
use actsmart\actsmart\Sensors\Slack\Events\SlackEventCreator;
use actsmart\actsmart\Controllers\Slack\ConversationController;
use actsmart\actsmart\Controllers\Slack\URLVerificationController;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Log\LoggerInterface;

class SlackAgent extends Agent
{
    private $slack_verification_token;

    private $slack_oauth_token;

    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger, $slack_verification_token, $slack_oauth_token)
    {
        parent::__construct($dispatcher, $logger);

        $this->slack_verification_token = $slack_verification_token;
        $this->slack_oauth_token = $slack_oauth_token;

        $this->configureForSlack();
    }

    private function configureForSlack()
    {
        // We use a store to keep external config
        $config_store = new ConfigStore();
        $config_store->add('token.slack', $this->slack_verification_token);
        $config_store->add('oauth_token.slack', $this->slack_oauth_token);
        $this->addComponent($config_store);

        // We need to pickup Slack events so need to add a Slack sensor
        $this->addComponent(new SlackSensor(new SlackEventCreator()));

        // A simple reactive controller for URL Verification.
        $this->addComponent(new URLVerificationController());
    }
}
