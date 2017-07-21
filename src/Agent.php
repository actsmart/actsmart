<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 21/07/2017
 * Time: 13:56
 */

namespace actsmart\actsmart;

use actsmart\actsmart\Sensors\SensorInterface;


class Agent
{
    /** @var  SensorInterface */
    protected $sensors;

    public function __construct() {

    }
}