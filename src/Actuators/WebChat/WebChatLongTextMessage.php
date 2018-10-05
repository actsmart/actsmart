<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatLongTextMessage extends WebChatMessage
{
    private $characterLimit = null;

    private $submitText = null;

    private $callbackId = null;

    private $initialText = null;

    private $placeholder = null;

    /**
     * @param $characterLimit
     * @return $this
     */
    public function setCharacterLimit($characterLimit)
    {
        $this->characterLimit = $characterLimit;
        return $this;
    }

    /**
     * @param $submitText
     * @return $this
     */
    public function setSubmitText($submitText)
    {
        $this->submitText = $submitText;
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
     * @return null|int
     */
    public function getCharacterLimit()
    {
        return $this->characterLimit;
    }

    /**
     * @return null|string
     */
    public function getSubmitText()
    {
        return $this->submitText;
    }

    /**
     * @return null|string
     */
    public function getCallbackId()
    {
        return $this->callbackId;
    }

    /**
     * @return null
     */
    public function getInitialText()
    {
        return $this->initialText;
    }

    /**
     * @param null $initialText
     */
    public function setInitialText($initialText)
    {
        $this->initialText = $initialText;
    }

    /**
     * @return null
     */
    public function getPlaceholder()
    {
        return $this->placeholder;
    }

    /**
     * @param null $placeholder
     */
    public function setPlaceholder($placeholder): void
    {
        $this->placeholder = $placeholder;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'longtext',
            'data' => [
                'text' => $this->getText(),
                'character_limit' => $this->getCharacterLimit(),
                'submit_text' => $this->getSubmitText(),
                'callback_id' => $this->getCallbackId(),
                'initial_text' => $this->getInitialText(),
                'placeholder' => $this->getPlaceholder(),
                'disable_text' => $this->getDisableText()
            ]
        ];
    }
}
