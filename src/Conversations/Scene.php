<?php

namespace actsmart\actsmart\Conversations;

use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;


class Scene extends Vertex
{
    /* @todo Added as a reminder that Scenes can have pre and post conditions */
    private $preconditions = [];

    private $postconditions = [];

}