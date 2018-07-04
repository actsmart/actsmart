<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Interpreters\BasicMessageInterpreter;
use PHPUnit\Framework\TestCase;

class InterpreterTest extends TestCase
{
    public function testStripSlashCommands()
    {
        $interpreter = new BasicMessageInterpreter();

        $message = "/teamchecklist-local /teamchecklist help";
        $after = $interpreter->removeAllCommands($message);
        $this->assertEquals("help", $after);

        $message = "/teamchecklist-local ";
        $after = $interpreter->removeAllCommands($message);
        $this->assertEquals("", $after);
    }

    public function testCleanseMessage()
    {
        $interpreter = new BasicMessageInterpreter();

        $message = "/teamchecklist <@stuarth> help";
        $after = $interpreter->cleanseMessage($message);
        $this->assertEquals("help", $after);
    }
}
