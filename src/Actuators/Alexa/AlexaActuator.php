<?php

namespace actsmart\actsmart\Actuators\Alexa;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use Ds\Map;
use GuzzleHttp\Client;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Interacts with Alexa to post messages.
 */
class AlexaActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const POST_MESSAGE_ACTION = 'action.alexa.postmessage';
    const KEY = 'actuator.alexa';

    private $headers = [];

    private $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @param $action
     * @param Map $arguments
     * @return mixed
     */
    public function perform(string $action, Map $arguments = null)
    {
        try {
            $message = $arguments->get(Literals::MESSAGE);
        } catch (\OutOfBoundsException $e) {
            return null;
        }

        $this->headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $response = null;

         return $this->postMessage($message);
    }

    /**
     * @param AlexaResponse $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function postMessage(AlexaResponse $message)
    {
        $this->logger->debug('Attempting a message');

        $this->agent->setHttpReaction(
            new JsonResponse(json_encode($message->getMessageToPost(), JSON_UNESCAPED_SLASHES),
                Response::HTTP_OK,
                $this->headers,
                true
            )
        );

        return $this->agent->httpReact();
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return self::KEY;
    }

    /**
     * @return array
     */
    public function performsActions()
    {
        return [self::POST_MESSAGE_ACTION];
    }
}
