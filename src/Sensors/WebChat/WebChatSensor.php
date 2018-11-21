<?php

namespace actsmart\actsmart\Sensors\WebChat;

use actsmart\actsmart\Sensors\ReadMessageEvent;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatEventCreator;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

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

    public function receive($message)
    {
        $this->logger->debug('Got a message: ' . print_r($message,1));
        $message = json_decode($message);
        $event = $this->process($message);

        // Generate a read receipt.
        $this->notify(ReadMessageEvent::EVENT_NAME,
            new ReadMessageEvent($message, ['message_id' => $event->getMessageId(), 'user_id' => $event->getUserId()]));

        // Notify listeners of the actual event.
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
        } else if ($message->type === WebChatEventCreator::FORM_RESPONSE) {
            $event_type = WebChatEventCreator::FORM_RESPONSE;
        } else if ($message->type === WebChatEventCreator::LIST_RESPONSE) {
            $event_type = WebChatEventCreator::LIST_RESPONSE;
        } else if ($message->type == WebChatEventCreator::CHAT_OPEN) {
            $event_type = WebChatEventCreator::CHAT_OPEN;
        } else if (isset($message->data->callback_id)) {
            $event_type = WebChatEventCreator::ACTION;
        }

        return $this->eventCreator->createEvent($event_type, $message);
    }
}
