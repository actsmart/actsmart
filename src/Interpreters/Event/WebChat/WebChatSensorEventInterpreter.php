<?php

namespace actsmart\actsmart\Interpreters\Event\Webchat;

use actsmart\actsmart\Interpreters\Event\BaseEventInterpreter;
use actsmart\actsmart\Sensors\WebChat\Events\ActionEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ChatOpenEvent;
use actsmart\actsmart\Sensors\WebChat\Events\FormEvent;
use actsmart\actsmart\Sensors\WebChat\Events\LongTextEvent;
use actsmart\actsmart\Sensors\WebChat\Events\MessageEvent;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatEvent;
use actsmart\actsmart\Sensors\WebChat\Events\WebChatUtteranceEvent;
use Symfony\Component\EventDispatcher\GenericEvent;

class WebChatSensorEventInterpreter extends BaseEventInterpreter
{
    const KEY = 'interpreter.webchat.sensor_event';

    public function listen(GenericEvent $e)
    {
        if ($e instanceof WebChatEvent) {
            $utteranceEvent = new WebChatUtteranceEvent($e);
            $this->notify($utteranceEvent->getKey(), $utteranceEvent);
        }
    }

    public function listensForEvents()
    {

        return array(ActionEvent::EVENT_NAME, FormEvent::EVENT_NAME, ChatOpenEvent::EVENT_NAME, MessageEvent::EVENT_NAME, LongTextEvent::EVENT_NAME);

    }

    public function getKey()
    {
        return static::KEY;
    }
}
