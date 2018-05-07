<?php
namespace actsmart\actsmart\Stores\Slack;

use actsmart\actsmart\Stores\ConfigRequestEvent;
use actsmart\actsmart\Stores\ConfigStore;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class SlackConfigStore
 * @package actsmart\actsmart\Stores\Slack
 *
 * The Slack config store makes use of a specific actuator to retrieve information related to Slack when that information
 * has not already been set via different means.
 */
class SlackConfigStore extends ConfigStore
{
    /**
     * Listens for ConfigRequest events and when it is about the slackworkspace it calls an
     * action.slack.getbotinfo action on an implementic actuator. If you are not setting these values explicitly you
     * should provide an actuator that responds to this action request.
     *
     * @param GenericEvent $event
     */
    public function listen(GenericEvent $event)
    {
        parent::listen($event);

        if ($event instanceof ConfigRequestEvent) {
            // Check if the topic is a slack workspace
            if (in_array('slackworkspace', explode('_', $event->getArgument('topic')))) {
                if ((isset($this->configuration[$event->getArgument('topic')][$event->getArgument('key')]))) {
                    return;
                } else {
                    $workspace_id = explode('_', $event->getArgument('topic'))[1];
                    $this->getAgent()->performAction('action.slack.getbotinfo', ['workspace_id' => $workspace_id]);
                }
            }
        }
    }
}
