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

    public function createEvent($event_type)
    {
        return new $this->slack_event_map[$event_type];
    }
}