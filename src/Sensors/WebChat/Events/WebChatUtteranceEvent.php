<?php

namespace actsmart\actsmart\Sensors\WebChat\Events;

use actsmart\actsmart\Sensors\UtteranceEvent;
use Ds\Map;
use Symfony\Component\EventDispatcher\GenericEvent;

class WebChatUtteranceEvent extends GenericEvent implements UtteranceEvent
{
    const KEY = 'event.webchat.utterance';

    /** @var Map */
    private $utterance;

    public function __construct(WebChatEvent $e)
    {
        parent::__construct($e->getSubject(), $e->getArguments());
        $this->utterance = $e->getUtterance();
    }

    public function getUtterance(): Map
    {
        return $this->utterance;
    }

    public function getKey()
    {
        return self::KEY;
    }
}
