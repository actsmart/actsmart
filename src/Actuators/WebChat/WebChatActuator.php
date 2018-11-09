<?php

namespace actsmart\actsmart\Actuators\WebChat;

use actsmart\actsmart\Actuators\ActuatorInterface;
use actsmart\actsmart\Sensors\WebChat\Events\ResponseActionEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ResponseFormEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ResponseImageEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ResponseLongTextEvent;
use actsmart\actsmart\Sensors\WebChat\Events\ResponseMessageEvent;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\Literals;
use actsmart\actsmart\Utils\NotifierInterface;
use actsmart\actsmart\Utils\NotifierTrait;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class WebChatActuator implements ComponentInterface, LoggerAwareInterface, ActuatorInterface, NotifierInterface
{
    use LoggerAwareTrait, ComponentTrait, NotifierTrait;

    const KEY           = 'actuator.webchat';
    const POST_MESSAGE  = 'action.webchat.postmessage';

    private $headers = [];

    /**
     * WebChat messages are sent by replying to the original request
     *
     * @param string $action
     * @param Map $arguments
     * @return mixed
     */
    public function perform(string $action, Map $arguments)
    {
        if ($action != self::POST_MESSAGE || !$arguments->hasKey('message')) {
            return null;
        }

        $this->headers = [
            'Content-Type' => 'application/json; charset=utf-8',
        ];

        $response = null;

        if (is_array($arguments->get('message'))) {
            return $this->postMultipleMessages($arguments->get('message'), $arguments->get('user_id'));
        }

        return $this->postMessage($arguments->get('message'), $arguments->get('user_id'));
    }

    /**
     * @param WebChatMessage $message
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    protected function postMessage(WebChatMessage $message, $user_id)
    {
        $this->logger->debug('Attempting a Web Chat response message.', $message->getMessageToPost());

        $this->notifyMessageEvent($message, $user_id);

        $this->agent->setHttpReaction(
            new JsonResponse($message->getMessageToPost(),
                Response::HTTP_OK,
                $this->headers
            )
        );

        return $this->agent->httpReact();
    }

    protected function postMultipleMessages(array $messages, $user_id)
    {
        $this->logger->debug('Attempting a Web Chat response message.');

        $response = [];
        foreach ($messages as $message) {
            $response[] = $message->getMessageToPost();

            $this->notifyMessageEvent($message, $user_id);
        }

        $this->agent->setHttpReaction(
            new JsonResponse($response,
                Response::HTTP_OK,
                $this->headers
            )
        );
    }

    protected function notifyMessageEvent(WebChatMessage $message, $user_id)
    {
        switch (true) {
            case $message instanceof WebChatImageMessage:
                $event = new ResponseImageEvent($message, [Literals::USER_ID => $user_id]);
                break;
            case $message instanceof WebChatButtonMessage:
                $event = new ResponseActionEvent($message, [Literals::USER_ID => $user_id]);
                break;
            case $message instanceof WebChatFormMessage:
                $event = new ResponseFormEvent($message, [Literals::USER_ID => $user_id]);
                break;
            case $message instanceof WebChatLongTextMessage:
                $event = new ResponseLongTextEvent($message, [Literals::USER_ID => $user_id]);
                break;
            default:
                $event = new ResponseMessageEvent($message, [Literals::USER_ID => $user_id]);
        }

        $this->notify($event->getkey(), $event);
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
