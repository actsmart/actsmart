<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatMessage
{
    /** The message text. */
    private $text = null;

    private $disable_text = false;

    /**
     * Sets text for a standard Web Chat message. The main text is escaped
     *
     * @param $format - main message text
     * @param array $args - replaced in format
     * @return $this
     */
    public function setText($format, $args = [])
    {
        // Escape &, <, > characters
        $this->text = vsprintf(htmlspecialchars($format, ENT_NOQUOTES), $args);
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

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'text',
            'data' => [
                'text' => $this->getText(),
                'disable_text' => $this->getDisableText()
            ]
        ];
    }
}
