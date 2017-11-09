<?php

namespace actsmart\actsmart\Utils;

use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Trait NotifierTrait
 * @package actsmart\actsmart\Utils
 *
 * Basic implementation of NotifierInterface
 */
trait NotifierTrait
{
    /**
     * The dispatcher to use
     *
     * @var EventDispatcher
     */
    protected $dispatcher;

    /**
     * Sets a dispatcher
     *
     * $param NotifierInterface $dispatcher
     */
    public function setDispatcher(EventDispatcher $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param GenericEvent $e
     */
    public function notify($event_key, GenericEvent $e)
    {
        if ($e) $this->dispatcher->dispatch($event_key, $e);
    }
}