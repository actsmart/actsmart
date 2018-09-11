<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Conversations\ConditionInterface;
use actsmart\actsmart\Interpreters\Intent;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;
use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Interpreters\IntentInterpreter;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Stores\StoreInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\NotifierInterface;

class Agent
{
    /** @var  array */
    protected $sensors = [];

    /** @var  array */
    protected $actuators = [];

    /** @var array - the set of actions that actuators can perform */
    protected $actions = [];

    /** @var  array */
    protected $controllers = [];

    /** @var array */
    protected $stores = [];

    /** @var array  */
    protected $intent_interpreters = [];

    /** @var IntentInterpreter */
    protected $default_intent_interpreter;

    /** @var array */
    protected $intent_conditions = [];

    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var  \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var  Response */
    protected $http_response = null;

    /**
     * Agent constructor.
     *
     * @param EventDispatcher $dispatcher
     * @param LoggerInterface $logger
     */
    public function __construct(EventDispatcher $dispatcher, LoggerInterface $logger)
    {
        $this->dispatcher = $dispatcher;
        $this->logger = $logger;

        // Add the conversation controller as a component
    }

    /**
     * Based on the type of component it gets added to the appropriate array. Also any other services that the components
     * may need are bound.
     *
     * @param ComponentInterface $component
     */
    public function addComponent(ComponentInterface $component)
    {
        switch (true) {
            case $component instanceof SensorInterface:
                $this->sensors[$component->getKey()] = $component;
                break;
            case $component instanceof ActuatorInterface:
                $this->actuators[$component->getKey()] = $component;
                // Register all the actions as well
                $this->actions[$component->getKey()] = $component->performsActions();
                break;
            case $component instanceof ControllerInterface:
                $this->controllers[$component->getKey()] = $component;
                break;
            case $component instanceof StoreInterface:
                $this->stores[$component->getKey()] = $component;
                break;
            case $component instanceof IntentInterpreter:
                $this->intent_interpreters[$component->getKey()] = $component;
                break;
            case $component instanceof ConditionInterface:
                $this->conditions[$component->getKey()] = $component;
                break;
        }

        //Inject the agent in the components.
        $component->setAgent($this);

        $this->bindLogger($component);
        $this->bindDispatcher($component);
        $this->bindListener($component);

        return $this;
    }

    /**
     * Returns a store based on the key.
     *
     * @param $key
     * @return mixed
     */
    public function getStore($key)
    {
        return $this->stores[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getSensor($key)
    {
        return $this->sensors[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getIntentInterpreter($key)
    {
        if (!isset($this->intent_interpreters[$key])) {
            throw new IntentInterpretDoesNotExistException('No interpret with key ' . $key . ' exists.');
        }

        return $this->intent_interpreters[$key];
    }

    /**
     * @return IntentInterpreter
     */
    public function getDefaultIntentInterpreter()
    {
        if (!isset($this->default_intent_interpreter)) {
            throw new DefaultIntentInterpreterNotDefinedException('This agent does not have a default intent interpreter defined.');
        }
        return $this->default_intent_interpreter;
    }

    /**
     * @param $key
     */
    public function setDefaultIntentInterpreter($key)
    {
        $this->default_intent_interpreter = $this->getIntentInterpreter($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getActuator($key)
    {
        return $this->actuators[$key];
    }

    /**
     * @return EventDispatcher
     */
    public function getEventDispatcher()
    {
        return $this->dispatcher;
    }

    /**
     * @param $action_id
     * @param $object
     * @return mixed
     */
    public function performAction($action_id, $arguments = [])
    {
        foreach ($this->actions as $actuator => $actions) {
            if (in_array($action_id, $actions)) {
                return $this->getActuator($actuator)->perform($action_id, $arguments);
            }
        }
    }

    /**
     * @param $conditions
     * @param Map $utterance
     * @return bool
     */
    public function checkIntentConditions($conditions, Map $utterance)
    {
        foreach ($conditions as $condition) {
            if (!$this->intent_conditions[$condition]->check($utterance)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $interpreter_key
     * @param Map $utterance
     * @return Intent
     */
    public function interpretIntent($interpreter_key, Map $utterance)
    {
        foreach ($this->intent_interpreters as $key => $interpreter) {
            if ($interpreter_key == $key) {
                return $interpreter->interpretUtterance($utterance);
            }
        }

        return new Intent();
    }

    /**
     * @param Response $response
     */
    public function setHttpReaction(Response $response)
    {
        $this->http_response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function httpReact($status = Response::HTTP_OK)
    {
        if ($this->http_response == null) {
            return new Response('', $status, ['content-type' => 'text/html']);
        }
        return $this->http_response;
    }

    /**
     * @param ComponentInterface $component
     */
    private function bindListener($component)
    {
        if ($component instanceof ListenerInterface) {
            $this->listenForEvents($component);
        }
    }

    /**
     * Registers a listener for all the events that the listener itself says they are interested in.
     *
     * @param ListenerInterface $listener
     */
    private function listenForEvents(ListenerInterface $listener)
    {
        foreach ($listener->listensForEvents() as $event_key) {
            $this->dispatcher->addListener($event_key, array($listener, 'listen'), $listener->getPriority());
        }
    }

    /**
     * Adds the logger to classes that are LaggerAware
     * @param ComponentInterface $component
     */
    private function bindLogger($component)
    {
        if ($component instanceof LoggerAwareInterface) {
            $component->setLogger($this->logger);
        }
    }

    /**
     * Adds the dispatcher to classes that are Notifiers
     * @param ComponentInterface $component
     */
    private function bindDispatcher($component)
    {
        if ($component instanceof NotifierInterface) {
            $component->setDispatcher($this->dispatcher);
        }
    }
}
