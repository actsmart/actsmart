<?php

namespace actsmart\actsmart\Interpreters;

interface InterpreterInterface
{
    public function interpret($e);

    public function notify();

    public function getKey();
}