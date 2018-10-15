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
use Symfony\Component\EventDispatcher\GenericEvent;

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
     * Stores can listen to events and decide to store that information for access from other components or
     * perform other relevant actions.
     *
     * @inheritdoc
     */
    public function listensForEvents()
    {
        return [];
    }

    /**
     * Implementation of listen function. To be overridden as required.
     *
     * @param GenericEvent $e
     */
    public function listen(GenericEvent $e)
    {}

    /**
     * This is here as a default for stores that do not handle information requests.
     * @return array|string[]
     */
    public function handlesInformationRequests()
    {
        return [];
    }
}
