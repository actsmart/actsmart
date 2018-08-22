<?php

namespace actsmart\actsmart\Actuators\WebChat;

use actsmart\actsmart\Actuators\WebChat\WebChatButton;

class WebChatButtonMessage
{
    /** The message text. */
    private $text = null;

    private $buttons = [];

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

    public function addButton(WebChatButton $button)
    {
        $buttons[] = $button;
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
     * @return array
     */
    public function getButtons()
    {
        return $this->buttons;
    }

    /**
     * @return array
     */
    public function getButtonsArray()
    {
        $buttons = [];

        foreach ($this->buttons as $button) {
            $buttons[] = [
                'text' => $button->getText(),
                'callback_id' => $button->getCallbackId()
            ];
        }

        return $buttons;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'button',
            'data' => [
                'text' => $this->getText(),
                'buttons' => $this->getButtonsArray()
            ]
        ];
    }
}
