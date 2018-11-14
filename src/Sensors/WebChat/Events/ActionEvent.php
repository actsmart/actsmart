<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ActionEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.action';
    const CALLBACK_DELIMITER = '.';

    private $callbackId = null;

    /**
     * Callback data is additional information contained within a callback string. We use a . as the separator between
     * the data elements.
     * @var array|null
     */
    private $callbackData = null;

    private $text;

    private $value;

    protected $event_key;

    public function __construct($subject, $arguments = [], $key = null)
    {
        $this->event_key = $key == null ? self::EVENT_NAME : $key;
        parent::__construct($subject, $arguments, $this->event_key);

        $this->callbackId = $this->extractCallbackId($subject->data->callback_id);
        $this->callbackData = $this->extractCallbackData($subject->data->callback_id);
        $this->user = $subject->user ?? null;
        $this->userId = $subject->user_id;
        $this->text = $subject->data->text ?? null;
        $this->value = $subject->data->value ?? null;
    }

    public function getKey()
    {
        return $this->event_key;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = parent::getUtterance();

        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_ACTION);
        $utterance->put(Literals::CALLBACK_ID, $this->callbackId);
        $utterance->put(Literals::CALLBACK_DATA, $this->callbackData);
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::TEXT, $this->text);
        $utterance->put(Literals::VALUE, $this->value);

        return $utterance;
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return mixed
     */
    public function getCallbackData()
    {
        return $this->callbackData;
    }

    /**
     * @param string $callback
     * @return string|null
     */
    private function extractCallbackId(string $callback)
    {
        $data = explode(self::CALLBACK_DELIMITER, $callback);
        return isset($data[0]) ? $data[0] : null;
    }

    /**
     * @param string $callback
     * @return array|null
     */
    private function extractCallbackData(string $callback)
    {
        $data = explode(self::CALLBACK_DELIMITER, $callback);
        if (count($data) > 1) {
            array_shift($data);
            return $data;
        } else {
            return null;
        }
    }
}
