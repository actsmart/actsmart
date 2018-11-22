<?php

namespace actsmart\actsmart\Actuators\WebChat;

class WebChatImageMessage extends WebChatMessage
{
    private $imgSrc = null;

    private $imgLink = null;

    private $linkNewTab = true;

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
     * @param $linkNewTab
     * @return $this
     */
    public function setLinkNewTab($linkNewTab)
    {
        $this->linkNewTab = $linkNewTab;
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

    /**
     * @return bool
     */
    public function getLinkNewTab()
    {
        return $this->linkNewTab;
    }

    public function getData()
    {
        return [
            'img_src' => $this->getImgSrc(),
            'img_link' => $this->getImgLink(),
            'link_new_tab' => $this->getLinkNewTab()
        ];
    }

    public function getMessageToPost()
    {
        return [
            'author' => 'them',
            'type' => 'image',
            'data' => $this->getData()
        ];
    }
}
