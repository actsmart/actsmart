<?php

namespace actsmart\actsmart\Actuators\WebChat\Button;

abstract class BaseWebchatButton
{
    protected $text = null;

    /**
     * @param $text
     * @return $this
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return null|string
     */
    public function getText()
    {
        return $this->text;
    }

    public function getData()
    {
        return [
            'text' => $this->getText()
        ];
    }
}
