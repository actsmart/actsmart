<?php

namespace actsmart\actsmart\Utils;

use actsmart\actsmart\Agent;

trait ComponentTrait
{
    /**
     * @var \actsmart\actsmart\Agent
     */
    protected $agent;

    public function setAgent(Agent $agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }
}
