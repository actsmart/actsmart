<?php

namespace actsmart\actsmart\Sensors\Alexa\Events;

use actsmart\actsmart\Domain\Alexa\Slot;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Sensors\UtteranceEvent;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;

/**
 * The base Alexa event class that all Alexa Event classes should extend
 */
class AlexaMessageEvent extends SensorEvent implements UtteranceEvent
{
    const KEY = 'event.alexa.message';

    protected $requestId = null;

    protected $userId = null;

    protected $sessionId = null;

    protected $message = null;

    protected $intent = null;

    private $dialogState = null;

    /** @var Slot[] */
    private $slots;

    public function __construct($subject, $arguments, $event_key = 'event.alexa.generic')
    {
        parent::__construct($subject, $arguments);

        $this->event_key = $event_key;

        $this->userId = $subject->session->user->userId;
        $this->intent = $subject->request->intent->name ?? null;

        $this->dialogState = $subject->request->dialogState ?? null;

        $this->extractSlots($subject->request->intent);
    }

    private function extractSlots($intent)
    {
        if (isset($intent->slots)) {
            foreach ($intent->slots as $slotRaw) {
                $slot = Slot::fromRaw($slotRaw);
                $this->slots[$slot->name] = $slot;
            }
        }
    }

    /**
     * @return null
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @return null
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @return null
     */
    public function getSessionId()
    {
        return $this->sessionId;
    }

    /**
     * @return null
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return null
     */
    public function getIntent()
    {
        return $this->intent;
    }

    /**
     * @return null
     */
    public function getDialogState()
    {
        return $this->dialogState;
    }

    public function getUtterance(): Map
    {
        /* @var \Ds\Map */
        $utterance = new Map();
        $utterance->put(Literals::REQUEST_ID, $this->getRequestId());
        $utterance->put(Literals::USER_ID, $this->getUserId());
        $utterance->put(Literals::SESSION_ID, $this->getSessionId());
        $utterance->put(Literals::MESSAGE, $this->getMessage());
        $utterance->put(Literals::INTENT, $this->getIntent());
        $utterance->put(Literals::DIALOG_STATE, $this->getDialogState());
        $utterance->put(Literals::SLOTS, $this->slots);

        return $utterance;
    }

    public function getKey()
    {
        return self::KEY;
    }
}
