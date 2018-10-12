<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\Facebook\FacebookActuator;
use actsmart\actsmart\Controllers\Facebook\ConversationController;
use actsmart\actsmart\Controllers\Facebook\URLVerificationController;
use actsmart\actsmart\Sensors\Facebook\Events\FacebookEventCreator;
use actsmart\actsmart\Sensors\Facebook\FacebookSensor;
use actsmart\actsmart\Stores\ContextStore;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

class FacebookAgent extends Agent
{
    // The verification token associated with the Facebook app we will be using.
    private $facebook_verification_token;

    // The base uri for the Facebook api - useful to manage for testing, proxying, etc.
    private $facebook_base_uri;

    // Set to true if we should reply immediately to Facebook callbacks (to avoid duplicated events) - set to false for debugging other parts of the application.
    private $facebook_reply_early;

    private $facebook_access_token;

    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger, string $facebook_verification_token, string $facebook_base_uri = null, bool $facebook_reply_early = null, $facebook_access_token)
    {
        parent::__construct($dispatcher, $logger);

        $this->facebook_verification_token = $facebook_verification_token;
        $this->facebook_base_uri = $facebook_base_uri;
        $this->facebook_reply_early = $facebook_reply_early;
        $this->facebook_access_token = $facebook_access_token;

        $this->configureForFacebook();
    }

    /**
     * Setup the generic components required for a FacebookAgent.
     */
    private function configureForFacebook()
    {
        $config_store = new ConfigStore();
        $config_store->add('facebook', 'app.token', $this->facebook_verification_token);
        $config_store->add('facebook', 'reply_early', $this->facebook_reply_early);
        $config_store->add('facebook', 'uri.base', $this->facebook_base_uri);
        $config_store->add('facebook', 'access.token', $this->facebook_access_token);

        $this->addComponent($config_store);

        $this->addComponent(new FacebookSensor(new FacebookEventCreator()));

        // A simple controller for URL Verification.
        $this->addComponent(new URLVerificationController());

        $this->addComponent(new ConversationController());

        $this->addComponent(new FacebookActuator());
    }
}
