<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class BaseStore implements LoggerAwareInterface, ComponentInterface, StoreInterface, ListenerInterface, NotifierInterface
{
    use LoggerAwareTrait, ComponentTrait, ListenerTrait, NotifierTrait;

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * Stores can listen to events and decide to store that information for access from other components.
     *
     * @inheritdoc
     */
    public function listensForEvents()
    {
        return [];
    }
}
