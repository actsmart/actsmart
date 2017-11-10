<?php

namespace actsmart\actsmart\Actions;

use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Stores\ContextStore;
use Symfony\Component\EventDispatcher\EventDispatcher;

abstract class AbstractAction implements ActionInterface
{

    /**
     * Use to notify
     * @var EventDispatcher;
     */
    private $event_dispatcher;

    private $store;

    public function __construct(EventDispatcher $event_dispatcher, ContextStore $store)
    {
        $this->event_dispatcher = $event_dispatcher;
        $this->store = $store;
        $event_dispatcher->addListener($this->getActionName(), array($store, 'store'));
    }

    public function notify(ActionEvent $a)
    {
        $this->event_dispatcher->dispatch($this->getActionName(), $a);
    }
}
