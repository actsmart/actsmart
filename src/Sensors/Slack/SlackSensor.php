<?php

namespace actsmart\actsmart\Sensors\Slack;

use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\Slack\Events\SlackEventCreator;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;

/**
 * Class SlackSensor
 * @package actsmart\actsmart\Sensors
 */
class SlackSensor implements SensorInterface, NotifierInterface, ComponentInterface, LoggerAwareInterface
{
    use NotifierTrait, ComponentTrait, LoggerAwareTrait;

    const SENSOR_NAME = 'sensor.slack';

    /**
     * The class that creates events based on the input.
     *
     * @var SlackEventCreator
     */
    private $event_creator;

    public function __construct(SlackEventCreator $event_creator)
    {
        $this->event_creator = $event_creator;
    }

    /**
     * @param SymfonyRequest $message
     */
    public function receive(SymfonyRequest $message)
    {
        $this->logger->debug('Got a message: ' . $message->getContent());

        switch ($message->getContentType()) {
            case 'json':
                $slack_message = json_decode($message->getContent());
                break;
            case 'form':
                // If we are getting form content it is either json stuffed in the payload attribute
                // or as actual form content
                $this->logger->debug(implode($message->request->all()));

                if ($message->get('payload') != null) {
                    $slack_message = json_decode(urldecode($message->get('payload')));
                } else {
                    $slack_message = (object)$message->request->all();
                    // Add a command type for commands.
                    if (isset($slack_message->command)) {
                        $slack_message->type = 'command';
                    }
                }
                break;
            default:
                $this->logger->debug('Could not get message content.');
                return false;
        }

        if ($this->validateSlackMessage($slack_message)) {
            if ($this->getAgent()->getStore('store.config')->get('slack', 'reply_early') && ($slack_message->type != 'url_verification')) {
                // Reply to Slack so we don't get a retry unless it's a URL verification event.
                $this->getAgent()->httpReact()->send();
            }

            if ($event = $this->process($slack_message)) {
                $this->notify($event->getkey(), $event);
            }
        }
    }

    /**
     * Process the slack message and creates an appropriate Slack event based on the message type.
     * @param $slack_message
     * @return Events\SlackEvent|null
     */
    public function process($slack_message)
    {
        try {
            switch ($slack_message->type) {
                case 'url_verification':
                    return $this->event_creator->createEvent($slack_message->type, $slack_message);
                    break;
                case 'event_callback':
                    // If it is an event callback then we need to check whether the message has a subtype as well
                    $message_type = isset($slack_message->event->subtype) ? $slack_message->event->subtype : $slack_message->event->type;
                    return $this->event_creator->createEvent($message_type, $slack_message);
                    break;
                case 'interactive_message':
                    return $this->event_creator->createEvent($slack_message->type, $slack_message);
                    break;
                case 'command':
                    return $this->event_creator->createEvent($slack_message->type, $slack_message);
            }
        } catch (SlackEventTypeNotSupportedException $e) {
            $this->logger->notice('Unsupported Slack message');
            return null;
        }
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::SENSOR_NAME;
    }


    /**
     * @param $slack_message
     * @return bool
     * @throws \Exception
     */
    private function validateSlackMessage($slack_message)
    {
        if ($slack_message->token != $this->agent->getStore('store.config')->get('slack', 'app.token')) {
            throw new SlackMessageInvalidException("Could not validate Slack Message");
        } else {
            return true;
        }
    }
}
