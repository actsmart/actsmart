<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatListMessage extends WebChatMessage
{
    private $elements = [];

    /**
     * @param WebChatListElement $element
     * @return $this
     */
    public function addElement(WebChatListElement $element)
    {
      $this->elements[] = $element;
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
     * @return array
     */
    public function getElementsArray()
    {
        $elements = [];

        foreach ($this->elements as $element) {
            $elements[] = [
                'title' => $element->getTitle(),
                'subtitle' => $element->getSubTitle(),
                'image' => $element->getImage(),
                'button' => [
                    'text' => $element->getButtonText(),
                    'callback' => $element->getButtonCallback(),
                    'url' => $element->getButtonUrl(),
                    'link_new_tab' => $element->getButtonLinkNewTab()
                ]
            ];
        }

        return $elements;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'list',
            'data' => [
                'elements' => $this->getElementsArray()
            ]
        ];
    }
}
