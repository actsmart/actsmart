<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatImageMessage extends WebChatMessage
{
    private $imgSrc = null;

    private $imgLink = null;

    /**
     * @param $imgSrc
     * @return $this
     */
    public function setImgSrc($imgSrc)
    {
        $this->imgSrc = $imgSrc;
        return $this;
    }

    /**
     * @param $imgLink
     * @return $this
     */
    public function setImgLink($imgLink)
    {
        $this->imgLink = $imgLink;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getImgSrc()
    {
        return $this->imgSrc;
    }

    /**
     * @return null|string
     */
    public function getImgLink()
    {
        return $this->imgLink;
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'image',
            'data' => [
                'img_src' => $this->getImgSrc(),
                'img_link' => $this->getImgLink()
            ]
        ];
    }
}
