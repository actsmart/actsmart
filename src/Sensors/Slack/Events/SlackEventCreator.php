<?php

namespace actsmart\actsmart\Sensors\Slack\Events;

use actsmart\actsmart\Sensors\Slack\SlackEventTypeNotSupportedException;
use actsmart\actsmart\Utils\Literals;

class SlackEventCreator
{
    /**
     * Map a Slack event type to a SlackEvent class
     * @var array
     */
    public $slack_event_map = [
        'url_verification' => SlackUrlVerificationEvent::class,
        Literals::MESSAGE => SlackMessageEvent::class,
        'app_mention' => SlackMessageEvent::class,
        'interactive_message' => SlackInteractiveMessageEvent::class,
        'command' => SlackCommandEvent::class,
        'message_action' => SlackMessageActionEvent::class,
        'tokens_revoked' => SlackTokensRevokedEvent::class,
        'dialog_submission' => SlackDialogSubmissionEvent::class,
    ];

    /**
     * @param $event_type
     * @param $message
     * @return SlackEvent
     */
    public function createEvent($event_type, $message)
    {
        if ($this->supportsEvent($event_type)) {
            return new $this->slack_event_map[$event_type]($message, [$event_type]);
        } else {
            throw new SlackEventTypeNotSupportedException("Unsupported Slack event type " . $event_type);
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
