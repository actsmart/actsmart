<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use UnexpectedValueException;

class WebChatEventCreator
{
    const MESSAGE            = 'message';
    const ACTION             = 'action';
    const FORM_RESPONSE      = 'webchat_form_response';
    const LIST_RESPONSE      = 'webchat_list_response';
    const LONGTEXT_RESPONSE  = 'longtext_response';
    const CHAT_OPEN          = 'chat_open';

    public $eventMap = [
        self::MESSAGE => MessageEvent::class,
        self::ACTION => ActionEvent::class,
        self::FORM_RESPONSE => FormEvent::class,
        self::LIST_RESPONSE => ListEvent::class,
        self::LONGTEXT_RESPONSE => LongTextEvent::class,
        self::CHAT_OPEN => ChatOpenEvent::class
    ];

    /**
     * @param $event_type
     * @param $message
     * @return WebChatEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->eventMap[$event_type]($message, [$event_type]);
        } else {
            throw new UnexpectedValueException("Unsupported Web Chat event type " . $event_type);
        }
    }

    /**
     * @param $event_type
     * @return bool
     */
    public function supportsEvent($event_type)
    {
        if (key_exists($event_type, $this->eventMap)) {
            return true;
        } else {
            return false;
        }
    }
}
