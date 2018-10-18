<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Utils\Literals;
use Ds\Map;

class ChatOpenEvent extends WebChatEvent
{
    const EVENT_NAME = 'event.webchat.chat_open';

    private $userIPAddress = null;

    private $userCountry = null;

    private $userBrowserLanguage = null;

    private $userOS = null;

    private $userBrowser = null;

    private $userTimezone = null;

    private $callbackId = null;

    protected $event_key;

    public function __construct($subject, $arguments = [], $key = null)
    {
        $this->event_key = $key == null ? self::EVENT_NAME : $key;
        parent::__construct($subject, $arguments, $this->event_key);

        $this->userId = $subject->user_id;
        $this->timestamp = time();

        $this->userIPAddress = $subject->data->user->ip_address ?? null;
        $this->userCountry = $subject->data->user->country ?? null;
        $this->userBrowserLanguage = $subject->data->user->browser_language ?? null;
        $this->userOS = $subject->data->user->os ?? null;
        $this->userBrowser = $subject->data->user->browser ?? null;
        $this->userTimezone = $subject->data->user->timezone ?? null;
        $this->callbackId = $subject->data->callback_id ?? null;
    }

    public function getKey()
    {
        return $this->event_key;
    }

    // This needs to go away but we have to clean up the hierarchy from SensorEvent to WebChatEvent
    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::TYPE, Literals::WEB_CHAT_OPEN);
        $utterance->put(Literals::CALLBACK_ID, $this->callbackId);
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::TIMESTAMP, $this->getTimestamp());
        $utterance->put(Literals::TEXT, '');

        return $utterance;
    }

    /**
     * @return string
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getUserIPAddress()
    {
        return $this->userIPAddress;
    }

    /**
     * @return string
     */
    public function getUserCountry()
    {
        return $this->userCountry;
    }

    /**
     * @return string
     */
    public function getUserBrowserLanguage()
    {
        return $this->userBrowserLanguage;
    }

    /**
     * @return string
     */
    public function getUserOS()
    {
        return $this->userOS;
    }

    /**
     * @return string
     */
    public function getUserBrowser()
    {
        return $this->userBrowser;
    }

    /**
     * @return string
     */
    public function getUserTimezone()
    {
        return $this->userTimezone;
    }

    /**
     * @return string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
