<?php
/**
 * Created by PhpStorm.
 * User: ronaldashri
 * Date: 27/07/2017
 * Time: 08:47
 */

namespace actsmart\actsmart\Interpreters;

class BaseInterpreter implements InterpreterInterface
{
    public function interpret($e)
    {
        // Interpret
        //...

        $this->notify();
        return $e;
    }

    public function notify()
    {
    }
}
