<?php

namespace actsmart\actsmart\Actuators\WebChat;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WebChatActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface
{
    use LoggerAwareTrait, ComponentTrait;

    const KEY           = 'actuator.webchat';
    const POST_MESSAGE  = 'action.webchat.postmessage';

    private $headers = [];

    /**
     * WebChat messages are sent by replying to the original request
     * TODO this is using arguments as an array. Actuators expect a MAP
     * @param string $action
     * @param Map $arguments
     * @return mixed
     */
    public function perform(string $action, $arguments)
    {
        if ($action != self::POST_MESSAGE || !isset($arguments['message'])) {
            return null;
        }

        $this->headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $response = null;

        if (is_array($arguments['message'])) {
            return $this->postMultipleMessages($arguments['message']);
        }

        return $this->postMessage($arguments['message']);
    }

    /**
     * @param WebChatMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function postMessage(WebChatMessage $message)
    {
        $this->logger->debug('Attempting a Web Chat response message.', $message->getMessageToPost());

        $this->agent->setHttpReaction(
            new JsonResponse($message->getMessageToPost(),
                Response::HTTP_OK,
                $this->headers
            )
        );

        return $this->agent->httpReact();
    }

    protected function postMultipleMessages(array $messages)
    {
        $this->logger->debug('Attempting a Web Chat response message.');

        $response = [];
        foreach ($messages as $message) {
            $response[] = $message->getMessageToPost();
        }

        $this->agent->setHttpReaction(
            new JsonResponse($response,
                Response::HTTP_OK,
                $this->headers
            )
        );
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
        return [self::POST_MESSAGE];
    }
}
