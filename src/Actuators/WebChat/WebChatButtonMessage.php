<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatButtonMessage extends WebChatMessage
{
    protected $messageType = 'button';

    /** The message buttons. @var WebChatButton[] */
    private $buttons = [];

    /**
     * @param WebChatButton $button
     * @return $this
     */
    public function addButton(WebChatButton $button)
    {
        $this->buttons[] = $button;
        return $this;
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
    public function getData()
    {
        return parent::getData() + [
            'buttons' => $this->getButtonsArray()
        ];
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
                'callback_id' => $button->getCallbackId(),
                'value' => $button->getValue()
            ];
        }

        return $buttons;
    }
}
