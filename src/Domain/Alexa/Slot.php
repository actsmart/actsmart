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
}
/*

"slots": {
    "player_sent_off": {
        "name": "player_sent_off",
					"value": "goal keeper",
					"resolutions": {
            "resolutionsPerAuthority": [
							{
                                "authority": "amzn1.er-authority.echo-sdk.amzn1.ask.skill.f1779828-4cd5-4342-91de-09aa59536871.player_sent_off",
								"status": {
                                "code": "ER_SUCCESS_MATCH"
								},
								"values": [
									{
                                        "value": {
                                        "name": "goal keeper",
											"id": "d42f51644ac301da305a647fe93e6e74"
										}
									}
								]
							}
						]
					},
					"confirmationStatus": "NONE",
					"source": "USER"
				}
			}

*/