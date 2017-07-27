<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 14:36
 */

namespace actsmart\actsmart\Controllers\Slack;

use Symfony\Component\HttpFoundation\Response;
use actsmart\actsmart\Agent;
use actsmart\actsmart\Sensors\SensorEvent;
use actsmart\actsmart\Controllers\Reactive\ReactiveController;

class URLVerificationController extends ReactiveController
{
    private $slack_verification_token;

    /** @var  Agent */
    private $agent;

    public function __construct(Agent $agent, $slack_verification_token)
    {
        $this->agent = $agent;

    }
    public function execute(SensorEvent $e = null)
    {
        $this->agent->setHttpReaction(
            new Response('Contasdfsaent',
            Response::HTTP_BAD_GATEWAY,
                ['content-type' => 'texddt/html']
            )
        );
    }

}