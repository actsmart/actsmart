<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatFormTextareaElement extends WebChatFormElement
{
    /**
     * @return array
     */
    public function getData()
    {
        return parent::getData() + [
            'element_type' => 'textarea'
        ];
    }
}
