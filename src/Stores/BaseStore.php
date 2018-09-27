<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Ds\Map;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;

abstract class BaseStore implements LoggerAwareInterface, ComponentInterface, StoreInterface
{
    use LoggerAwareTrait, ComponentTrait;

    /**
     * The logger instance.
     *
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @inheritdoc
     */
    public function getInformation($information_request_id, Map $arguments)
    {
        return null;
    }

    /**
     * This is here as a default for stores that do not support information requests
     *
     * @inheritdoc
     */
    public function handlesInformationRequests()
    {
        return [];
    }
}
