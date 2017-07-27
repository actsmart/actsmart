<?php

namespace actsmart\actsmart\Interpreters;


interface InterpretorInterface
{
    public function receive($e);

    public function interpret($e);

    public function notify();
}