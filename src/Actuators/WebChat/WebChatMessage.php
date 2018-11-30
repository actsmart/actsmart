<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatMessage
{
    protected $messageType = 'text';

    /** The message text. */
    private $text = null;

    private $disable_text = false;

    private $time;

    private $date;

    private $hidetime = false;

    private $internal = false;

    private $isEmpty = false;

    public function __construct()
    {
        $this->time = date('h:i A');
        $this->date = date('D j M');
    }

    /**
     * Sets text for a standard Web Chat message. The main text is escaped
     *
     * @param $format - main message text
     * @param array $args - replaced in format
     * @param bool - skip special chars encoding
     * @return $this
     */
    public function setText($format, $args = [], bool $noSpecialChars = false)
    {
        if ($noSpecialChars) {
            $this->text = vsprintf($format, $args);
        } else {
            // Escape &, <, > characters
            $this->text = vsprintf(htmlspecialchars($format, ENT_NOQUOTES), $args);
        }
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
     * Set disable_text property
     *
     * @param $disable_text
     * @return $this
     */
    public function setDisableText($disable_text)
    {
        $this->disable_text = $disable_text;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDisableText()
    {
        return $this->disable_text;
    }

    /**
     * @return string
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @return string
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @return bool
     */
    public function getInternal()
    {
        return $this->internal;
    }

    /**
     * Set internal property
     *
     * @param $internal
     * @return $this
     */
    public function setInternal($internal)
    {
        $this->internal = $internal;
        return $this;
    }

    /**
     * @return bool
     */
    public function getHidetime()
    {
        return $this->hidetime;
    }

    /**
     * Set hidetime property
     *
     * @param $hidetime
     * @return $this
     */
    public function setHidetime($hidetime)
    {
        $this->hidetime = $hidetime;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->isEmpty;
    }

    public function setAsEmpty(): void
    {
        $this->isEmpty = true;
    }


    /**
     * @return array
     */
    public function getData()
    {
        return [
            'text' => $this->getText(),
            'disable_text' => $this->getDisableText(),
            'internal' => $this->getInternal(),
            'hidetime' => $this->getHidetime(),
            'time' => $this->getTime(),
            'date' => $this->getDate()
        ];
    }

    public function getMessageToPost()
    {
        if ($this->isEmpty) {
            return false;
        }
        return [
            'author' => 'them',
            'type' => $this->messageType,
            'data' => $this->getData()
        ];
    }
}
