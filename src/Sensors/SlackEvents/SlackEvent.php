<?php


namespace actsmart\actsmart\Sensors\SlackEvents;


class SlackEvent
{
    /**
     * The original slack message - json encoded object
     * @var object
     */
    private $message;

    /**
     * @var string
     */
    private $type;

    public function __construct($type, $message)
    {
        $this->type = $type;

        $this->message = $message;
    }


}