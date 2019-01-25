<?php

namespace actsmart\actsmart\Actuators\WebChat\Form;

use actsmart\actsmart\Actuators\WebChat\WebChatFormElement;

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
