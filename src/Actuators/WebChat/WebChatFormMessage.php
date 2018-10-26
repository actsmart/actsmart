<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatFormMessage extends WebChatMessage
{
    private $text = null;

    private $submitText = 'Submit';

    private $elements = [];

    private $callbackId = null;

    /**
     * @param WebChatFormElement $element
     * @return $this
     */
    public function addElement(WebChatFormElement $element)
    {
      $this->elements[] = $element;
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
     * @return array
     */
    public function getElements()
    {
        return $this->elements;
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
     * @return array
     */
    public function getElementsArray()
    {
        $elements = [];

        foreach ($this->elements as $element) {
            $elements[] = [
                'name' => $element->getName(),
                'display' => $element->getDisplay(),
                'required' => $element->getRequired()
            ];
        }

        return $elements;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'webchat_form',
            'data' => [
                'callback_id' => $this->getCallbackId(),
                'text' => $this->getText(),
                'elements' => $this->getElementsArray(),
                'submit_text' => $this->getSubmitText()
            ]
        ];
    }
}
