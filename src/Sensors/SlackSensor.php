<?php

namespace actsmart\actsmart\Sensors;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class SlackSensor implements SensorInterface
{
    /**
     * The slack token corresponding to the Slack application
     *
     * @var string
     */
    private $slack_token;

    /**
     * The class that creates events based on the input.
     *
     * @var SlackEventCreator
     */
    private $event_creator;

    public function __construct($slack_token, SlackEventCreator $event_creator)
    {
        $this->slack_token = $slack_token;
        $this->event_creator = $event_creator;
    }

    public function receive(SymfonyRequest $message)
    {
        $slack_message = json_decode($message->getContent());

        // Determine what type of message this is
        try {
            $e = $this->event_creator->createEvent($slack_message->type);
            var_dump($e);
            return $this->event_creator->createEvent($slack_message->type);
        } catch (Exception $e) {

        }
    }

        

    public function process()
    {

    }

    public function notify()
    {

    }
}