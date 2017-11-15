<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Actuators\ActionEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class ConfigStore
 * @package actsmart\actsmart\Stores
 *
 * Stores any configuration that various components may require.
 */
class ConfigStore implements ComponentInterface, StoreInterface, ListenerInterface
{
    use ComponentTrait;

    private $configuration = [];

    /**
     * @param $key
     * @param $value
     */
    public function add($key, $value)
    {
        $this->configuration[$key] = $value;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {
        if (!isset($this->configuration[$key])) {
            throw new ConfigurationStoreValueNotSetException('Value for ' . $key . ' not set');
        }
        return $this->configuration[$key];
    }

    /**
     * @param GenericEvent $e
     */
    public function listen(GenericEvent  $e) {
        if ($e instanceof ActionEvent) {
            $subject = $e->getSubject();
            foreach ($subject as $key => $value) {
                $this->add($key, $value);
            }
        }
    }

    public function listensForEvents() {
        return ['event.action.oauth_token.slack.information'];
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return 'store.config';
    }
}
