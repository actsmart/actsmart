<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Controllers\Facebook\URLVerificationController;
use actsmart\actsmart\Sensors\Facebook\Events\FacebookEventCreator;
use actsmart\actsmart\Sensors\Facebook\FacebookSensor;
use actsmart\actsmart\Stores\ConfigStore;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Psr\Log\LoggerInterface;

class FacebookAgent extends Agent
{
    // The verification token associated with the Slack app we will be using.
    private $facebook_verification_token;

    // The base uri for the Facebook api - useful to manage for testing, proxying, etc.
    private $facebook_base_uri;

    // Set to true if we should reply immediately to Facebook callbacks (to avoid duplicated events) - set to false for debugging other parts of the application.
    private $facebook_reply_early;

    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger, string $facebook_verification_token, string $facebook_base_uri = null, bool $facebook_reply_early = null)
    {
        parent::__construct($dispatcher, $logger);

        $this->facebook_verification_token = $facebook_verification_token;
        $this->slack_base_uri = $facebook_base_uri;
        $this->slack_reply_early = $facebook_reply_early;

        $this->configureForFacebook();
    }

    /**
     * Setup the generic components required for a SlackAgent.
     */
    private function configureForFacebook()
    {
        $config_store = new ConfigStore();
        $config_store->add('facebook', 'app.token', $this->facebook_verification_token);
        $config_store->add('facebook', 'reply_early', $this->facebook_reply_early);

        $this->addComponent($config_store);

        $this->addComponent(new FacebookSensor(new FacebookEventCreator()));

        // A simple controller for URL Verification.
        $this->addComponent(new URLVerificationController());
    }
}
