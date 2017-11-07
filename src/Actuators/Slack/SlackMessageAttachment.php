<?php

namespace actsmart\actsmart\Actuators\Slack;

use actsmart\actsmart\Actuators\Slack\SlackMessageAttachmentField;
use actsmart\actsmart\Actuators\Slack\SlackMessageAttachmentAction;

class SlackMessageAttachment
{
    private $fallback;

    private $color;

    private $pretext;

    private $authorname;

    private $author_link;

    private $author_icon;

    private $title;

    private $title_link;

    private $text;

    private $fields = [];

    private $image_url;

    private $thumb_url;

    private $footer;

    private $footer_icon;

    private $timestamp;

    private $callback_id;

    private $actions = [];

    private $attachment_type = 'default';

    /**
     * @return mixed
     */
    public function getFallback()
    {
        return $this->fallback;
    }

    /**
     * @param mixed $fallback
     * @return SlackMessageAttachment
     */
    public function setFallback($fallback)
    {
        $this->fallback = $fallback;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param mixed $color
     * @return SlackMessageAttachment
     */
    public function setColor($color)
    {
        $this->color = $color;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPretext()
    {
        return $this->pretext;
    }

    /**
     * @param mixed $pretext
     * @return SlackMessageAttachment
     */
    public function setPretext($pretext)
    {
        $this->pretext = $pretext;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorname()
    {
        return $this->authorname;
    }

    /**
     * @param mixed $authorname
     * @return SlackMessageAttachment
     */
    public function setAuthorname($authorname)
    {
        $this->authorname = $authorname;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorLink()
    {
        return $this->author_link;
    }

    /**
     * @param mixed $author_link
     * @return SlackMessageAttachment
     */
    public function setAuthorLink($author_link)
    {
        $this->author_link = $author_link;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getAuthorIcon()
    {
        return $this->author_icon;
    }

    /**
     * @param mixed $author_icon
     * @return SlackMessageAttachment
     */
    public function setAuthorIcon($author_icon)
    {
        $this->author_icon = $author_icon;
        return $this;
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
     * @return SlackMessageAttachment
     */
    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTitleLink()
    {
        return $this->title_link;
    }

    /**
     * @param mixed $title_link
     * @return SlackMessageAttachment
     */
    public function setTitleLink($title_link)
    {
        $this->title_link = $title_link;
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
     * @return SlackMessageAttachment
     */
    public function setText($text)
    {
        $this->text = $text;
        return $this;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }

    /**
     * @param array $fields
     * @return SlackMessageAttachment
     */
    public function setFields($fields)
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Creates a new field and adds it to the attachment.
     * @param $title
     * @param $value
     * @param $short
     * @return $this
     */
    public function createField($title, $value, $short)
    {
        $this->addField(new SlackMessageAttachmentField($title, $value, $short));
        return $this;
    }

    /**
     * Adds a field to the attachment.
     * @param \actsmart\actsmart\Actuators\Slack\SlackMessageAttachmentField $field
     * @return $this
     */
    public function addField(SlackMessageAttachmentField $field)
    {
        $this->fields[] = $field;
        return $this;
    }

    /**
     * @return array
     */
    public function getFieldsToPost()
    {
        $fields_to_post = [];
        foreach ($this->fields as $field) {
            $fields_to_post[] = $field->getFieldToPost();
        }
        return $fields_to_post;
    }

    /**
     * @return mixed
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * @param mixed $image_url
     * @return SlackMessageAttachment
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getThumbUrl()
    {
        return $this->thumb_url;
    }

    /**
     * @param mixed $thumb_url
     * @return SlackMessageAttachment
     */
    public function setThumbUrl($thumb_url)
    {
        $this->thumb_url = $thumb_url;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFooter()
    {
        return $this->footer;
    }

    /**
     * @param mixed $footer
     * @return SlackMessageAttachment
     */
    public function setFooter($footer)
    {
        $this->footer = $footer;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getFooterIcon()
    {
        return $this->footer_icon;
    }

    /**
     * @param mixed $footer_icon
     * @return SlackMessageAttachment
     */
    public function setFooterIcon($footer_icon)
    {
        $this->footer_icon = $footer_icon;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     * @return SlackMessageAttachment
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackId()
    {
        return $this->callback_id;
    }

    /**
     * @param mixed $callback_id
     */
    public function setCallbackId($callback_id)
    {
        $this->callback_id = $callback_id;
        return $this;
    }

    /**
     * @return array
     */
    public function getActions()
    {
        return $this->actions;
    }

    /**
     * @param array $actions
     */
    public function setActions($actions)
    {
        $this->actions = $actions;
        return $this;
    }

    /**
     * @param \actsmart\actsmart\Actuators\Slack\SlackMessageAttachmentAction $action
     * @return $this
     */
    public function addAction(SlackMessageAttachmentAction $action)
    {
        $this->actions[$action->getValue()] = $action;
        return $this;
    }

    /**
     * @return array
     */
    public function getActionsToPost()
    {
        $actions_to_post = [];
        foreach ($this->actions as $action) {
            $actions_to_post[] = $action->getActionToPost();
        }
        return $actions_to_post;
    }

    /**
     * @return string
     */
    public function getAttachmentType()
    {
        return $this->attachment_type;
    }

    /**
     * @param string $attachment_type
     */
    public function setAttachmentType($attachment_type)
    {
        $this->attachment_type = $attachment_type;
    }

    /**
     * Rebuilds the attachment and cycles through fields and actions as well.
     *
     * @todo Support all parameters.
     * @param $attachment
     */
    public function rebuildAttachment($attachment)
    {
        $this->callback_id = $attachment->callback_id;
        $this->title = $attachment->title;
        $this->fallback = $attachment->fallback;

        if (isset($attachment->fields)) {
            foreach ($attachment->fields as $field) {
                $new_field = new SlackMessageAttachmentField();
                $new_field->rebuildField($field);
                $this->addField($new_field);
            }
        }

        if (isset($attachment->actions)) {
            foreach ($attachment->actions as $action) {
                $new_action = new SlackMessageAttachmentAction();
                $new_action->rebuildAction($action);
                $this->addAction($new_action);
            }
        }
    }

    /**
     * Removes the action with the given value from the Attachment.
     * @param $value
     * @return bool
     */
    public function removeAction($value)
    {
        if (isset($this->actions[$value])) {
            unset($this->actions[$value]);
            return true;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAttachmentToPost()
    {
        $attachment = [
            'fallback' => $this->getFallback(),
            'color' => $this->getColor(),
            'pretext' => $this->getPretext(),
            'authorname' => $this->getAuthorname(),
            'author_link' => $this->getAuthorLink(),
            'author_icon' => $this->getAuthorIcon(),
            'title' => $this->getTitle(),
            'title_link' => $this->getTitleLink(),
            'text' => $this->getText(),
            'fields' =>$this->getFieldsToPost(),
            'image_url' => $this->getImageUrl(),
            'thumb_url' => $this->getThumbUrl(),
            'footer' => $this->getFooter(),
            'footer_icon' => $this->getFooterIcon(),
            'timestamp' => $this->getTimestamp(),
            "callback_id" => $this->getCallbackId(),
            'actions' => $this->getActionsToPost(),
        ];

        return $attachment;
    }
}
