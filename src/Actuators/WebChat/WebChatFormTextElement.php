<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatFormTextElement extends WebChatFormElement
{
    /**
     * @return array
     */
    public function getData()
    {
        return parent::getData() + [
            'element_type' => 'text'
        ];
    }
}
