<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\SensorEvent;
use Ds\Map;

class ChatOpenEvent extends SensorEvent
{
    const EVENT_NAME = 'event.webchat.chat_open';

    private $userId = null;

    private $timestamp = null;

    private $userIPAddress = null;

    private $userCountry = null;

    private $userBrowserLanguage = null;

    private $userOS = null;

    private $userBrowser = null;

    private $userTimezone = null;

    public function __construct($subject, $arguments = [])
    {
        parent::__construct($subject, $arguments = [], self::EVENT_NAME);

        $this->userId = $subject->user_id;
        $this->timestamp = time();

        $this->userIPAddress = $subject->data->user->ip_address ?? null;
        $this->userCountry = $subject->data->user->country ?? null;
        $this->userBrowserLanguage = $subject->data->user->browser_language ?? null;
        $this->userOS = $subject->data->user->os ?? null;
        $this->userBrowser = $subject->data->user->browser ?? null;
        $this->userTimezone = $subject->data->user->timezone ?? null;
    }

    public function getKey()
    {
        return self::EVENT_NAME;
    }

    // This needs to go away but we have to clean up the hierarchy from SensorEvent to WebChatEvent
    public function getUtterance() : Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
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
     * @return int
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
