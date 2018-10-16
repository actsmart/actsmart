<?php

namespace actsmart\actsmart;

use actsmart\actsmart\Stores\ContextInformation;
use actsmart\actsmart\Stores\ContextStore;
use PHPUnit\Framework\TestCase;

class ContextStoreTest extends TestCase
{
    public function testContextStoreGetInformation()
    {
        // Create a context store and store some information in it.
        $contextStore = new ContextStore();
        $contextInformation = new ContextInformation('test', 'test_id', '123');
        $contextStore->storeInformation($contextInformation);

        // Confirm we can retrieve the info stored.
        $newContextInformation = $contextStore->getInformation('test', 'test_id');
        $this->assertTrue($newContextInformation->getValue() == '123');

        // Confirm that if an id or a type is not present we get a null Information object.
        $new1ContextInformation = $contextStore->getInformation('test', 'test_not_id');
        $this->assertTrue($new1ContextInformation == null);

        $new2ContextInformation = $contextStore->getInformation('not_test', 'test_id');
        $this->assertTrue($new2ContextInformation == null);
    }
}
