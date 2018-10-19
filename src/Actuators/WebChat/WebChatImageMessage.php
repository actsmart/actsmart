<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatImageMessage extends WebChatMessage
{
    private $imgSrc = null;

    /**
     * @param $submitText
     * @return $this
     */
    public function setImgSrc($imgSrc)
    {
        $this->imgSrc = $imgSrc;
        return $this;
    }

    /**
     * @return array
     */
    public function getImgSrc()
    {
        return $this->imgSrc;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'image',
            'data' => [
                'img_src' => $this->getImgSrc()
            ]
        ];
    }
}
