<?php

namespace actsmart\actsmart\Tests;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Conversation\Intent;
use actsmart\actsmart\Conversations\Scene;
use actsmart\actsmart\Conversations\Participant;
use Fhaculty\Graph\Graph as Graph;
use Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Vertices;
use Fhaculty\Graph\Set\Edges;


use PHPUnit\Framework\TestCase;

class ConversationTest extends TestCase
{
    /**
     * Tests that the conversation builder returns a Graph instance as a conversation
     */
    public function testConversationInstantiation()
    {
        $conversation = new Conversation();
        $this->assertTrue(is_a($conversation, 'Fhaculty\Graph\Graph'));
    }

    public function testSceneCreation()
    {
        $conversation = new Conversation();
        $conversation->createScene('init');

        $vertex = $conversation->getScene('init');
        $this->assertTrue($vertex->getId() == 'init');
    }

    public function testSceneRetrieval()
    {
        $conversation = new Conversation();
        $conversation->createScene('one');
        $conversation->createScene('two');
        $conversation->createVertex('notascene');
        $scenes = $conversation->getScenes();
        $scene1 = $scenes->getVertexId('one');

        $notfound = false;
        try {
            $scenes->getVertexId('notascene');
        } catch(\Exception $e) {
          $notfound = true;
        }

        $this->assertTrue($notfound);
        $this->assertTrue($scene1->getId() == 'one');
    }

    public function testParticipantAddition()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2');

        // Retrieve both participants starting from the Scene
        $participants = $conversation->getParticipantsToScene('init');

        $this->assertTrue($participants->hasVertexId('init' . '/' . 'bot1'));
        $this->assertTrue($participants->hasVertexId('init' . '/' . 'bot2'));
        $this->assertTrue($participants->count() == 2);
    }

    public function testRetrievalOfSingleParticipantToScene()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addParticipantToScene('init', 'bot3');

        $participant = $conversation->getParticipantToScene('init', 'bot2');

        $this->assertTrue($participant->getId() == 'init/bot2');
    }

    /**
     * @group focus
     */
    public function testUtteranceAddition()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addUtterance('init', 'init', 'bot1', 'bot2', 0, new Intent(), new Message('Create list'))
            ->addUtterance('init', 'init', 'bot2', 'bot1', 1, new Intent(), new Message('Do you want a new list or a clone'))

            // New scene for create new list dialog
            ->createScene('new_list')
            ->addParticipantToScene('new_list', 'bot1')
            ->addParticipantToScene('new_list', 'bot2')
            ->addUtterance('init', 'new_list', 'bot1', 'bot2', 2, new Intent(), new Message('A new list'))
            ->addUtterance('new_list', 'new_list', 'bot1', 'bot2', 4, new Intent(), new Message('Call it MyList'))

            // New scene for clone list dialog
            ->createScene('clone_list')
            ->addParticipantToScene('clone_list', 'bot1')
            ->addParticipantToScene('clone_list', 'bot2')
            ->addUtterance('init', 'clone_list', 'bot1', 'bot2', 5, new Intent(), new Message('Clone an existing list'))
            ->addUtterance('clone_list', 'clone_list', 'bot2', 'bot1', 6, new Intent(), new Message('select a list'));

        $init_utterances = $conversation->getAllUtterancesForScene('init');

        // There should be four utterances
        $this->assertTrue($init_utterances->count() == 4, 'Correct number of utterances');

        // Check the first utterance for init scene
        $this->assertTrue($init_utterances->getEdgeFirst()->getMessage()->getTextResponse() == 'Create list');

        // Check the last utterance of init scene
        $this->assertTrue($init_utterances->getEdgeLast()->getMessage()->getTextResponse() == 'Clone an existing list', 'Checking last utterance');


        $new_list_utterances = $conversation->getAllUtterancesForScene('init');

    }

    public function testSceneChange()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addUtterance('init', 'init', 'bot1', 'bot2', 0, new Intent(), new Message('Create list'))
            ->addUtterance('init', 'init', 'bot2', 'bot1', 1, new Intent(), new Message('Do you want a new list or a clone'))

            // New scene for create new list dialog
            ->createScene('new_list')
            ->addParticipantToScene('new_list', 'bot1')
            ->addParticipantToScene('new_list', 'bot2')
            ->addUtterance('init', 'new_list', 'bot1', 'bot2', 2, new Intent(), new Message('A new list'))
            ->addUtterance('new_list', 'new_list', 'bot1', 'bot2', 3, new Intent(), new Message('Call it MyList'));

        // Get the utterances of of bot1
        $utterances = $conversation->getScene('init')->getParticipant('bot1')->getUtterances();

        foreach ($utterances as $utterance){
            if ($utterance->getMessage()->getTextResponse() == 'Create list')
            {
                $this->assertFalse($utterance->changesScene());
            }

            if ($utterance->getMessage()->getTextResponse() == 'A new list')
            {
                $this->assertTrue($utterance->changesScene());
            }
        }

    }

    public function testExitUtterances()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addUtterance('init', 'init', 'bot1', 'bot2', 0, new Intent(), new Message('Create list'))
            ->addUtterance('init', 'init', 'bot2', 'bot1', 1, new Intent(), new Message('Do you want a new list or a clone'))

            // New scene for create new list dialog
            ->createScene('new_list')
            ->addParticipantToScene('new_list', 'bot1')
            ->addParticipantToScene('new_list', 'bot2')
            ->addUtterance('init', 'new_list', 'bot1', 'bot2', 2, new Intent(), new Message('A new list'))
            ->addUtterance('new_list', 'new_list', 'bot1', 'bot2', 3, new Intent(), new Message('Call it MyList'))

            // New scene for clone list dialog
            ->createScene('clone_list')
            ->addParticipantToScene('clone_list', 'bot1')
            ->addParticipantToScene('clone_list', 'bot2')
            ->addUtterance('init', 'clone_list', 'bot1', 'bot2', 4, new Intent(), new Message('Clone an existing list'))
            ->addUtterance('clone_list', 'clone_list', 'bot2', 'bot1', 5, new Intent(), new Message('select a list'));


        $exit_utterances = $conversation->getScene('init')->getExitUtterances();

        $this->assertTrue($exit_utterances->getEdgeFirst()->getMessage()->getResponse() == 'A new list');
        $this->assertTrue($exit_utterances->getEdgeLast()->getMessage()->getResponse() == 'Clone an existing list');
    }

    public function testInternalUtterances()
    {
        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addUtterance('init', 'init', 'bot1', 'bot2', 0, new Intent(), new Message('Create list'))
            ->addUtterance('init', 'init', 'bot2', 'bot1', 1, new Intent(), new Message('Do you want a new list or a clone'))

            // New scene for create new list dialog
            ->createScene('new_list')
            ->addParticipantToScene('new_list', 'bot1')
            ->addParticipantToScene('new_list', 'bot2')
            ->addUtterance('init', 'new_list', 'bot1', 'bot2', 2, new Intent(), new Message('A new list'))
            ->addUtterance('new_list', 'new_list', 'bot1', 'bot2', 3, new Intent(), new Message('Call it MyList'))

            // New scene for clone list dialog
            ->createScene('clone_list')
            ->addParticipantToScene('clone_list', 'bot1')
            ->addParticipantToScene('clone_list', 'bot2')
            ->addUtterance('init', 'clone_list', 'bot1', 'bot2', 4, new Intent(), new Message('Clone an existing list'))
            ->addUtterance('clone_list', 'clone_list', 'bot2', 'bot1', 5, new Intent(), new Message('select a list'));


        $internal_utterances = $conversation->getScene('init')->getInternalUtterances();

        $this->assertTrue($internal_utterances->getEdgeFirst()->getMessage()->getTextResponse() == 'Create list');
        $this->assertTrue($internal_utterances->getEdgeLast()->getMessage()->getTextResponse() == 'Do you want a new list or a clone');
    }


}
