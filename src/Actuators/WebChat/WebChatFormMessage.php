<?php

namespace actsmart\actsmart\Actuators\WebChat;

use actsmart\actsmart\Actuators\WebChat\Form\WebChatFormElement;

class WebChatFormMessage extends WebChatMessage
{
    protected $messageType = 'webchat_form';

    private $submitText = 'Submit';

    /** @var WebChatFormElement[] */
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
     * @return WebChatFormElement[]
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
            $elements[] = $element->getData();
        }

        return $elements;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return parent::getData() + [
            'callback_id' => $this->getCallbackId(),
            'elements' => $this->getElementsArray(),
            'submit_text' => $this->getSubmitText()
        ];
    }
}
