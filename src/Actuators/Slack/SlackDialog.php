<?php

namespace actsmart\actsmart\Actuators\Slack;

class SlackDialog
{
    private $token;

    private $trigger_id;

    private $callback_id;

    public function __construct($token, $trigger_id, $callback_id)
    {
        $this->token = $token;
        $this->trigger_id = $trigger_id;
        $this->callback_id = $callback_id;
    }

    public function getDialogToPost()
    {
        $form_params = [
            'token' => $this->getToken(),
            'trigger_id' => $this->getTriggerId(),
            'dialog' => json_encode([
                'callback_id' => $this->getCallbackId(),
                'title' => 'Just a test',
                'submit_label' => 'Request',
                'elements' => [
                    [
                        'type' => 'text',
                        'label' => 'Label A',
                        'name' => 'label_a'
                    ],
                ],
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
}
