<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Actuators\ActionEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ConfigStore
 * @package actsmart\actsmart\Stores
 *
 * Stores any configuration that various components may require using a simple structure of
 * [topic][key][value]. The added topic level allows us to store multiple configuration settings
 * relating to a specific group or context.
 */
class ConfigStore implements ComponentInterface, StoreInterface, ListenerInterface, NotifierInterface
{
    use ComponentTrait, NotifierTrait;

    protected $configuration = [];

    /**
     * @param $topic
     * @param $key
     * @param $value
     */
    public function add($topic, $key, $value)
    {
        $this->configuration[$topic][$key] = $value;
    }

    /**
     * Retrieve the value associated with $key for a given $topic.
     * @param $topic
     * @param $key
     * @return mixed
     */
    public function get($topic, $key)
    {
        $this->notify('config.store.request', new ConfigRequestEvent(null, ['topic' => $topic, 'key' => $key]));
        if (!isset($this->configuration[$topic][$key])) {
            throw new ConfigurationStoreValueNotSetException('Value for ' . $key . ' not set');
        }
        return $this->configuration[$topic][$key];
    }

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
                    $this->add($topic, $key, $value);
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
        return 'store.config';
    }
}
