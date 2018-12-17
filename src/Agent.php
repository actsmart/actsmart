<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Controllers\ControllerInterface;
use actsmart\actsmart\Conversations\ConditionInterface;
use actsmart\actsmart\Interpreters\Intent\Intent;
use actsmart\actsmart\Interpreters\Intent\IntentInterpreter;
use actsmart\actsmart\Interpreters\KnowledgeGraph\KnowledgeGraphInterpreter;
use actsmart\actsmart\Interpreters\NLP\NLPAnalysis;
use actsmart\actsmart\Interpreters\NLP\NLPInterpreter;
use actsmart\actsmart\Sensors\SensorInterface;
use actsmart\actsmart\Stores\ContextInformation;
use actsmart\actsmart\Stores\ConversationInstanceStoreInterface;
use actsmart\actsmart\Stores\StoreInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\NotifierInterface;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class Agent
 *
 * The agent is a container class. It's task is to wire together all the
 * different components as well as provide as standard out of the box defaults.
 *
 * Typically you should be subclassing this class to configure for your own needs.
 *
 * @see WebChatAgent
 * @see SlackAgent
 *
 * @package actsmart\actsmart
 */
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

    /** @var array the set of information requests that the registered stores can perform */
    protected $information_requests = [];

    /** @var array  */
    protected $intent_interpreters = [];

    /** @var array */
    protected $nlp_interpreters = [];

    /** @var array */
    protected $kg_interpreters = [];

    /** @var IntentInterpreter */
    protected $default_intent_interpreter;

    /** @var NLPInterpreter */
    protected $default_nlp_interpreter;

    /** @var KnowledgeGraphInterpreter */
    protected $default_kg_interpreter;

    /** @var array */
    protected $intent_conditions = [];

    /** @var EventDispatcher */
    protected $dispatcher;

    /** @var  \Psr\Log\LoggerInterface */
    protected $logger;

    /** @var  Response */
    protected $http_response = null;

    /** @var ConversationInstanceStoreInterface */
    protected $conversationInstanceStore;

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
     * @return Agent
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
                $this->information_requests[$component->getKey()] = $component->handlesInformationRequests();
                break;
            case $component instanceof IntentInterpreter:
                $this->intent_interpreters[$component->getKey()] = $component;
                break;
            case $component instanceof NLPInterpreter:
                $this->nlp_interpreters[$component->getKey()] = $component;
                break;
            case $component instanceof KnowledgeGraphInterpreter:
                $this->kg_interpreters[$component->getKey()] = $component;
                break;
            case $component instanceof ConditionInterface:
                $this->intent_conditions[$component->getKey()] = $component;
                break;
            case $component instanceof ConversationInstanceStoreInterface:
                $this->conversationInstanceStore = $component;
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
     * @return StoreInterface
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
            throw new IntentInterpretDoesNotExistException('No intent interpreter with key ' . $key . ' exists.');
        }

        return $this->intent_interpreters[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getNLPInterpreter($key)
    {
        if (!isset($this->nlp_interpreters[$key])) {
            throw new NLPInterpretDoesNotExistException('No NLP interpreter with key ' . $key . ' exists.');
        }

        return $this->nlp_interpreters[$key];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getKGInterpreter($key)
    {
        if (!isset($this->kg_interpreters[$key])) {
            throw new KGInterpreterDoesNotExistException('No KG interpreter with key ' . $key . ' exists.');
        }

        return $this->kg_interpreters[$key];
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
     * @return ConversationInstanceStoreInterface
     */
    public function getConversationInstanceStore()
    {
        if (!isset($this->conversationInstanceStore)) {
            throw new ConversationInstanceStoreNotSetException('A conversation instance store has not been set');
        }
         return $this->conversationInstanceStore;
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
     * @param Map|null $arguments
     * @return mixed
     */
    public function performAction($action_id, Map $arguments = null)
    {
        foreach ($this->actions as $actuator => $actions) {
            if (in_array($action_id, $actions)) {
                return $this->getActuator($actuator)->perform($action_id, $arguments);
            }
        }

        $this->logger->info(sprintf('No supporting actuator found for action %', $action_id));
        return null;
    }

    /**
     * Loops through all available information requests and if they support the requested
     *
     * @param $information_request_id
     * @param Map $arguments
     * @return mixed|null
     */
    public function performInformationRequest($information_request_id, Map $arguments)
    {
        foreach ($this->information_requests as $store => $information_request_ids) {
            if (in_array($information_request_id, $information_request_ids)) {
                return $this->getStore($store)->getInformation($information_request_id, '', $arguments);
            }
        }

        $this->logger->info(sprintf('No supporting Store found for information request %', $information_request_id));
        return null;
    }

    /**
     * @param $conditions
     * @param Map $utterance
     * @return bool
     */
    public function checkIntentConditions($conditions, Map $utterance)
    {
        foreach ($conditions as $condition) {
            if (!$this->intent_conditions[$condition->getKey()]->check($utterance)) {
                return false;
            }
        }
        return true;
    }

    /**
     * @param string $intent_interpreter_key
     * @param Map $utterance
     * @return Intent
     */
    public function interpretIntent($intent_interpreter_key, Map $utterance)
    {
        if (array_key_exists($intent_interpreter_key, $this->intent_interpreters)) {
            return $this->intent_interpreters[$intent_interpreter_key]->interpretUtterance($utterance);
        }

        return new Intent();
    }

    /**
     * @param string $nlp_interpreter_key
     * @param Map $utterance
     * @return NLPAnalysis | null
     */
    public function interpretNLP($nlp_interpreter_key, Map $utterance)
    {
        if (array_key_exists($nlp_interpreter_key, $this->nlp_interpreters)) {
            return $this->nlp_interpreters[$nlp_interpreter_key]->analyse($utterance->get(Literals::TEXT));
        }

        return null;
    }

    /**
     * @param string $kg_interpreter_key
     * @param Map $utterance
     * @return NLPAnalysis | null
     */
    public function analyseKG($kg_interpreter_key, NLPAnalysis $nlp_analysis)
    {
        if (array_key_exists($kg_interpreter_key, $this->kg_interpreters)) {
            return $this->kg_interpreters[$kg_interpreter_key]->analyse($nlp_analysis);
        }

        return null;
    }

    /**
     * @param Response $response
     * @return Agent
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
     * Saves the context information against the given key. If a context store has not been bound to the agent, it raises
     * an alert level log message
     *
     * @param $key
     * @param $information
     * @return bool
     */
    public function saveContextInformation($key, $information)
    {
        if (!$store = $this->getStore(Literals::CONTEXT_STORE)) {
            $this->logger->alert(
                sprintf(
                    'Trying to save context information against key %s, but no context store has been bound',
                    $key));

            return false;
        }

        $information = new ContextInformation($key, '', $information);
        $store->storeInformation($information);

        return true;
    }

    /**
     * Gets a piece of context information from the store. If a context store has not been bound to the agent, it raises
     * an alert level log message
     *
     * @param $key
     * @return bool
     */
    public function getContextInformation($key)
    {
        if (!$store = $this->getStore(Literals::CONTEXT_STORE)) {
            $this->logger->alert(
                sprintf(
                    'Trying to save context information against key %s, but no context store has been bound',
                    $key));

            return false;
        }

        $information = $store->getInformation($key);

        if ($information) {
            return $information->getValue();
        }

        return false;
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
