<?php

namespace actsmart\actsmart\Actuators\Slack;

/**
 * Class SlackDialog
 * @package actsmart\actsmart\Actuators\Slack
 *
 * @todo - just a proof of concept for now - needs to provide generic tools for building dialogs.
 */
class SlackDialog
{
    private $token;

    private $trigger_id;

    private $callback_id;

    private $workspace;

    private $elements;

    private $title;

    private $submit_label;

    private $action;

    private $item_id;

    private $timestamp;

    private $response_url;

    public function __construct($token, $trigger_id, $callback_id, $workspace)
    {
        $this->token = $token;
        $this->trigger_id = $trigger_id;
        $this->callback_id = $callback_id;
        $this->workspace = $workspace;
    }

    public function getDialogToPost()
    {
        $form_params = [
            'token' => $this->getToken(),
            'trigger_id' => $this->getTriggerId(),
            'dialog' => json_encode([
                'callback_id' => $this->getCallbackId(),
                'title' => $this->title,
                'submit_label' => $this->submit_label,
                'elements' => $this->elements,
            ])
        ];

        return $form_params;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getTriggerId()
    {
        return $this->trigger_id;
    }

    /**
     * @param mixed $trigger_id
     */
    public function setTriggerId($trigger_id)
    {
        $this->trigger_id = $trigger_id;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getCallbackId()
    {
        if ($this->callback_id) {
            return $this->callback_id;
        }

        return "a:{$this->action};id:{$this->item_id};ts:{$this->timestamp};url:{$this->response_url}";
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
     * @return mixed
     */
    public function getWorkspace()
    {
        return $this->workspace;
    }

    /**
     * @param mixed $workspace
     */
    public function setWorkspace($workspace)
    {
        $this->workspace = $workspace;
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
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSubmitLabel()
    {
        return $this->submit_label;
    }

    /**
     * @param mixed $submit_label
     */
    public function setSubmitLabel($submit_label)
    {
        $this->submit_label = $submit_label;
    }

    /**
     * @param mixed $action
     */
    public function setAction($action)
    {
        $this->action = $action;
    }

    /**
     * @param mixed $item_id
     */
    public function setItemId($item_id)
    {
        $this->item_id = $item_id;
    }

    /**
     * @param mixed $response_url
     */
    public function setResponseUrl($response_url)
    {
        $this->response_url = $response_url;
    }

    /**
     * @param mixed $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }

    /**
     * @return mixed
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * @param mixed $elements
     */
    public function setElements($elements)
    {
        $this->elements = $elements;
    }
}
