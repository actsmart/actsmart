<?php

namespace actsmart\actsmart\Actuators\Slack;

class SlackMessageAttachmentField
{
    private $title;

    private $value;

    private $short;

    public function __construct($title, $value, $short)
    {
        $this->title = $title;
        $this->value = $value;
        $this->short = $short;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     * @return SlackMessageAttachmentFields
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     * @return SlackMessageAttachmentFields
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getShort()
    {
        return $this->short;
    }

    /**
     * @param mixed $short
     * @return SlackMessageAttachmentFields
     */
    public function setShort($short)
    {
        $this->short = $short;
        return $this;
    }


    public function getFieldToPost()
    {
        $field = [
            'title' => $this->getTitle(),
            'value' => $this->getValue(),
            'short' => $this->getShort()
        ];

        return $field;
    }
}