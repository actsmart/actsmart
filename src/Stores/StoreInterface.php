<?php
namespace actsmart\actsmart\Stores;

use Ds\Map;

interface StoreInterface
{
    /**
     * Retrieves an Information object based on the optional key and Map arguments.
     *
     * @param string $type - the type of information to retrieve
     * @param string $id - a unique identified for that info
     * @param Map $arguments - a set of arguments for the Store to apply to the information retrieval search
     * @return InformationInterface | null
     */
    public function getInformation(string $type, string $id = '', Map $arguments = null);


    /**
     * @param InformationInterface $information
     * @return mixed
     */
    public function storeInformation(InformationInterface $information);

    /**
     * Returns an array of information requests that this store can handle. This can be used to trigger
     * information requests based on speech acts.
     *
     * @return string[]
     */
    public function handlesInformationRequests();

}
