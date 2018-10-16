<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Actuators\ActionEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ContextStore
 * @package actsmart\actsmart\Stores
 *
 * Stores contextual information that various components may require using a simple structure of
 * [topic][key][value]. The added topic level allows us to store multiple configuration settings
 * relating to a specific group or context.
 */
class ContextStore extends EphemeralStore
{
    /**
     * Listens to events and registers the required info.
     * @param GenericEvent $e
     */
    public function listen(GenericEvent  $e)
    {
        if ($e instanceof ActionEvent) {
            $subject = $e->getSubject();
            foreach ($subject as $topic =>  $content) {
                foreach ($content as $key => $value) {
                    $this->storeInformation(new ContextInformation($subject, $topic, $content));
                }
            }
        }
    }

    public function listensForEvents()
    {
        return ['event.action.config.info', 'config.store.request'];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return Literals::CONTEXT_STORE;
    }
}
