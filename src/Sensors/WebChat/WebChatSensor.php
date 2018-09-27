<?php

namespace actsmart\actsmart\Sensors\WebChat;

use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatEventCreator;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

class WebChatSensor implements SensorInterface, NotifierInterface, ComponentInterface, LoggerAwareInterface
{
    use NotifierTrait, ComponentTrait, LoggerAwareTrait;

    const SENSOR_NAME = 'sensor.webchat';

    /**
     * The class that creates events based on the input.
     *
     * @var WebChatEventCreator
     */
    private $eventCreator;

    public function __construct(WebChatEventCreator $eventCreator)
    {
        $this->eventCreator = $eventCreator;
    }

    public function receive(SymfonyRequest $message)
    {
        if ($message->isMethod('get')) {
            $message = ($message->query->all());
            $message['type'] = 'chat_open';
            // Turn into object;
            $message = json_decode(json_encode($message));
        } elseif ($message->isMethod('post')) {
            $this->logger->debug('Got a message: ' . $message->getContent());

            $message = json_decode($message->getContent());
        }

        $event = $this->process($message);
        $this->notify($event->getkey(), $event);
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::SENSOR_NAME;
    }

    /**
     * Process the input.
     * @param $message
     * @return Events\WebChatEvent
     */
    public function process($message)
    {
        $event_type = WebChatEventCreator::MESSAGE;

        if ($message->type === WebChatEventCreator::LONGTEXT_RESPONSE) {
            $event_type = WebChatEventCreator::LONGTEXT_RESPONSE;

        } else if ($message->type == 'chat_open') {
            $event_type = WebChatEventCreator::CHAT_OPEN;
        }
        else if (isset($message->callback_id)) {
            $event_type = WebChatEventCreator::ACTION;
        }

        return $this->eventCreator->createEvent($event_type, $message);
    }
}
