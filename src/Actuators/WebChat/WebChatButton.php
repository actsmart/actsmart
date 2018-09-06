<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatButton
{
    private $text = null;

    private $callbackId = null;

    /**
     * @param $text
     * @param $callbackId
     */
    public function __construct($text, $callbackId)
    {
        $this->text = $text;
        $this->callbackId = $callbackId;
    }

    /**
     * @param $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @param $callbackId
     * @return $this
     */
    public function setCallbackId($callbackId)
    {
        $this->callbackId = $callbackId;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return null|string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }
}
