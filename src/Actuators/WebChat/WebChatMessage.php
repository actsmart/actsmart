<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatMessage
{
    /** The message text. */
    private $text = null;

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

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'text',
            'data' => [
                'text' => $this->getText()
            ]
        ];
    }
}
