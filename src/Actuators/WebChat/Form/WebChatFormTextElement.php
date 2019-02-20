<?php

namespace actsmart\actsmart\Actuators\WebChat\Form;

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
