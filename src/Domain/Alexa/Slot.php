<?php
namespace actsmart\actsmart\Domain\Alexa;

/**
 * A slot domain object to hold all information about an Alexa slot
 */
class Slot
{
    /** @var string */
    public $name;

    /** @var string */
    public $value;

    /** @var string */
    public $matchingSlot;

    /** @var bool */
    public $success;

    /** @var string */
    public $confirmationStatus;

    const SUCCESS = 'ER_SUCCESS_MATCH';

    static function fromRaw($raw)
    {
        $slot = new self;

        $slot->name = $raw->name;

        if (isset($raw->value)) {
            $slot->value = $raw->value;
        }

        if (isset($raw->confirmationStatus)) {
            $slot->confirmationStatus = $raw->confirmationStatus;
        }

        if (isset($raw->resolutions)) {
            foreach ($raw->resolutions->resolutionsPerAuthority as $resolution) {
                if ($resolution->status->code === self::SUCCESS) {
                    $slot->success = true;
                    $slot->matchingSlot = $resolution->values[0]->value->name;
                    continue;
                }
            }
        }

        return $slot;
    }

    /**
     * Will try to return the value of a yes/no slot as a boolean
     */
    public function getBooleanValue()
    {
        return strtolower($this->matchingSlot) === 'yes';
    }
}
