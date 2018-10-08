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

    private $userId = null;

    private $timestamp = null;

    private $text;

    protected $event_key;

    public function __construct($subject, $arguments = [], $key = null)
    {
        $this->event_key = $key == null ? self::EVENT_NAME : $key;
        parent::__construct($subject, $arguments, $this->event_key);

        $this->callbackId = $this->extractCallbackId($subject->data->callback_id);
        $this->callbackData = $this->extractCallbackData($subject->data->callback_id);
        $this->userId = $subject->user_id;
        $this->timestamp = time();
        $this->text = $subject->data->text ?? null;
    }

    public function getKey()
    {
        return $this->event_key;
    }

    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_ACTION);
        $utterance->put(Literals::CALLBACK_ID, $this->callbackId);
        $utterance->put(Literals::CALLBACK_DATA, $this->callbackData);
        $utterance->put(Literals::SOURCE_EVENT, $this);
        $utterance->put(Literals::UID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::TEXT, $this->text);

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
            $data = array_reverse($data);
            array_pop($data);
            return $data;
        } else {
            return null;
        }
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
