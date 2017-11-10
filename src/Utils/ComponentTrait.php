<?php

namespace actsmart\actsmart\Utils;

use actsmart\actsmart\Agent;

trait ComponentTrait
{

    /**
     * @var actsmart\actsmart\Agent
     */
    private $agent;

    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
    }
}
