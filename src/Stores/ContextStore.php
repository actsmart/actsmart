<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Actuators\ActionEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class ContextStore implements ComponentInterface, ListenerInterface, StoreInterface
{
    use ComponentTrait;

    private $context_info = [];

    public function listen(GenericEvent $a)
    {
        if ($a instanceof ActionEvent) {
            foreach ($a->getSubject() as $key => $value) {
                $this->context_info[$key] = $value;
            }
        }
    }

    /**
     * Sets a value in the context store.
     *
     * @param $key
     * @param $value
     */
    public function set($key, $value)
    {
        $this->context_info[$key] = $value;
    }

    /**
     * Retrieve a value from the context store.
     * @param $label
     * @return mixed
     */
    public function retrieve($label)
    {
        return $this->context_info[$label];
    }

    public function getKey()
    {
        return 'store.context';
    }

    public function listensForEvents()
    {
        return ['event.action.generic'];
    }
}
