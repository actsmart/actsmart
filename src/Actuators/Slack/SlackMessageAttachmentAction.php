<?php

namespace actsmart\actsmart\Actuators\Slack;

/**
 * @see https://api.slack.com/docs/interactive-message-field-guide#attachment_fields
 *
 * Class SlackMessageAttachmentAction
 * @package actsmart\actsmart\Actuators\Slack
 */
class SlackMessageAttachmentAction
{

    private $name;

    private $text;

    private $type;

    private $value;

    private $confirm;

    private $style;

    private $options = [];

    private $option_groups = [];

    private $data_source;

    private $selected_options = [];

    private $min_query_length;


    public function __construct($name, $text, $type, $value)
    {
        $this->name = $name;
        $this->text = $text;
        $this->type = $type;
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return SlackMessageAttachmentAction
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @param mixed $text
     * @return SlackMessageAttachmentAction
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param mixed $type
     * @return SlackMessageAttachmentAction
     */
    public function setType($type)
    {
        $this->type = $type;
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
     * @return SlackMessageAttachmentAction
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getConfirm()
    {
        return $this->confirm;
    }

    /**
     * @param mixed $confirm
     * @return SlackMessageAttachmentAction
     */
    public function setConfirm($confirm)
    {
        $this->confirm = $confirm;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getStyle()
    {
        return $this->style;
    }

    /**
     * @param mixed $style
     * @return SlackMessageAttachmentAction
     */
    public function setStyle($style)
    {
        $this->style = $style;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return SlackMessageAttachmentAction
     */
    public function setOptions($options)
    {
        $this->options = $options;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptionGroups()
    {
        return $this->option_groups;
    }

    /**
     * @param array $option_groups
     * @return SlackMessageAttachmentAction
     */
    public function setOptionGroups($option_groups)
    {
        $this->option_groups = $option_groups;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDataSource()
    {
        return $this->data_source;
    }

    /**
     * @param mixed $data_source
     * @return SlackMessageAttachmentAction
     */
    public function setDataSource($data_source)
    {
        $this->data_source = $data_source;
        return $this;
    }

    /**
     * @return array
     */
    public function getSelectedOptions()
    {
        return $this->selected_options;
    }

    /**
     * @param array $selected_options
     * @return SlackMessageAttachmentAction
     */
    public function setSelectedOptions($selected_options)
    {
        $this->selected_options = $selected_options;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getMinQueryLength()
    {
        return $this->min_query_length;
    }

    /**
     * @param mixed $min_query_length
     * @return SlackMessageAttachmentAction
     */
    public function setMinQueryLength($min_query_length)
    {
        $this->min_query_length = $min_query_length;
        return $this;
    }

    public function getActionToPost()
    {
        $action = [
            'name' => $this->getName(),
            'text' => $this->getText(),
            'type' => $this->getType(),
            'value' => $this->getValue(),
        ];

        // Need to handle callback values, differentiation of actions and make buttons idempotent
        return $action;
    }


}