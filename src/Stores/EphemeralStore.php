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
 * Stores information for the duration of a single call in a [type][id][value] structure.
 */
abstract class EphemeralStore extends BaseStore
{
    /* @var Map $store */
    protected $store;

    public function __construct() {
        $this->store = new Map();
    }

    public function storeInformation(InformationInterface $information)
    {
        if ($this->store->hasKey($information->getType())) {
            $typeStore = $this->store->get($information->getType());
            $typeStore[$information->getId()] = $information->getValue();
        } else {
            $typeStore = [$information->getId() => $information->getValue()];
            $this->store->put($information->getType(), $typeStore);
        }

        return $this;
    }

    public function getInformation(string $type = '', string $id = '', Map $arguments = null)
    {
        if ($this->informationExists($type, $id)) {
            $information = new ContextInformation($type, $id, $this->store->get($type)[$id]);
            return $information;
        }

        return null;
    }

    private function informationExists($type, $id) {
        if ($this->store->hasKey($type)) {
            if (isset($this->store->get($type)[$id])) {
                return true;
            }
        }
        return false;
    }
}
