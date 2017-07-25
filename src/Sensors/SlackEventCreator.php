<?php

namespace actsmart\actsmart\Sensors;

use actsmart\actsmart\Sensors\SlackEvents\SlackEvent;

class SlackEventCreator
{
    /**
     * Map a Slack event type to a SlackEvent class
     * @var array
     */
    public $slack_event_map = [
        'url_verification' => SlackEvent::class,
        ];

    /**
     * @param $event_type
     * @param $message
     * @return SlackEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->slack_event_map[$event_type]($event_type, $message);
        } else {
            throw new \Exception("Unsupported Slack event type " . $event_type);
        }
    }

    /**
     * @param $event_type
     * @return bool
     */
    public function supportsEvent($event_type)
    {
        if (key_exists($event_type, $this->slack_event_map)) {
            return true;
        } else {
            return false;
        }
    }
}