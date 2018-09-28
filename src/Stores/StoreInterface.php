<?php
namespace actsmart\actsmart\Stores;

use Ds\Map;

interface StoreInterface
{
    /**
     * @param $information_request_id string The id of the
     * @param $arguments
     * @return mixed
     */
    public function getInformation($information_request_id, Map $arguments);

    /**
     * Returns an array of information requests that will be listened to
     *
     * @return string[]
     */
    public function handlesInformationRequests();
}
