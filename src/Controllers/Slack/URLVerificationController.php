<?php

namespace actsmart\actsmart\Controllers\Slack;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use actsmart\actsmart\Utils\ListenerInterface;
use actsmart\actsmart\Utils\ListenerTrait;
use actsmart\actsmart\Utils\Literals;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Response;

class URLVerificationController implements ListenerInterface, ComponentInterface, LoggerAwareInterface
{
    use ComponentTrait, LoggerAwareTrait, ListenerTrait;

    public function listen(GenericEvent $e)
    {
        if ($e->getSubject()->token == $this->agent->getStore(Literals::CONTEXT_STORE)->getInformation('slack', 'app.token')) {
            $this->agent->setHttpReaction(
                new Response($e->getSubject()->challenge,
                    Response::HTTP_OK,
                    ['content-type' => 'text/html']
                )
            );
        }

        $this->getAgent()->httpReact()->send();

        // We have an actual response so let's stop propagation and allow the Agent to react directly.
        $e->stopPropagation();
    }

    public function getKey()
    {
        return 'controller.slack.url_verification';
    }

    public function listensForEvents()
    {
        return ['event.slack.url_verification'];
    }
}
