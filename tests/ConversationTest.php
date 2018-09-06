<?php

namespace actsmart\actsmart\Tests;

use actsmart\actsmart\Conversations\Conversation;
use actsmart\actsmart\Conversations\Message;
use actsmart\actsmart\Interpreters\Intent\Intent;
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

    public function testSceneClassInstanceRetrieval()
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
        } catch (\Exception $e) {
            $notfound = true;
        }

        $this->assertTrue($notfound);
        $this->assertTrue($scene1->getId() == 'one');
    }

    public function testGetAllScenes()
    {
        $conversation = new Conversation();
        $conversation->createScene('init');
        $conversation->createScene('two');
        $conversation->createVertex('somethingelse');

        $scenes = $conversation->getScenes();

        $this->assertTrue($scenes->hasVertexId('init'));
        $this->assertTrue($scenes->hasVertexId('two'));
        $this->assertFalse($scenes->hasVertexId('somethingelse'));
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

    public function testUtteranceAddition()
    {
        $conversation = $this->buildConversation();

        $init_utterances = $conversation->getAllUtterancesForScene('init');

        // There should be four utterances
        $this->assertTrue($init_utterances->count() == 4, 'Correct number of utterances');

        // Check the first utterance for init scene
        $this->assertTrue($init_utterances->getEdgeFirst()->getMessage()->getTextResponse() == 'Create list', 'First utterance of init scene');

        // Check the last utterance of init scene
        $this->assertTrue($init_utterances->getEdgeLast()->getMessage()->getTextResponse() == 'Clone an existing list', 'Checking last utterance', 'Last utterance of init scene');
    }

    public function testSceneChange()
    {
        $conversation = $this->buildConversation();

        // Get the utterances of of bot1
        $utterances = $conversation->getScene('init')->getParticipant('bot1')->getUtterances();

        foreach ($utterances as $utterance) {
            if ($utterance->getMessage()->getTextResponse() == 'Create list') {
                $this->assertFalse($utterance->changesScene());
            }

            if ($utterance->getMessage()->getTextResponse() == 'A new list') {
                $this->assertTrue($utterance->changesScene());
            }
        }
    }

    public function testExitUtterances()
    {
        $conversation = $this->buildConversation();

        $exit_utterances = $conversation->getScene('init')->getExitUtterances();

        $this->assertTrue($exit_utterances->getEdgeFirst()->getMessage()->getTextResponse() == 'A new list');
        $this->assertTrue($exit_utterances->getEdgeLast()->getMessage()->getTextResponse() == 'Clone an existing list');
    }

    public function testInternalUtterances()
    {
        $conversation = $this->buildConversation();

        $internal_utterances = $conversation->getScene('init')->getInternalUtterances();

        $this->assertTrue($internal_utterances->getEdgeFirst()->getMessage()->getTextResponse() == 'Create list');
        $this->assertTrue($internal_utterances->getEdgeLast()->getMessage()->getTextResponse() == 'Do you want a new list or a clone');
    }

    public function testGetAllUtterances()
    {
        $conversation = $this->buildConversation();

        $utterances = $conversation->getAllUtterancesKeyedBySequence();

        $this->assertTrue($utterances[0]->getMessage()->getTextResponse() == 'Create list', 'Testing 0');
        $this->assertTrue($utterances[1]->getMessage()->getTextResponse() == 'Do you want a new list or a clone', 'Testing 1');
        $this->assertTrue($utterances[2]->getMessage()->getTextResponse() == 'A new list', 'Testing 2');
        $this->assertTrue($utterances[5]->getMessage()->getTextResponse() == 'A new list has been created. All done.', 'Testing 5');
        $this->assertTrue($utterances[9]->getMessage()->getTextResponse() == 'Onboarding list cloned', 'Testing 10');
    }

    /**
     * @group focus
     */
    public function testPossibleFollowUps()
    {
        $conversation = $this->buildConversation();

        $current_sequence = 1;
        $current_scene = 'init';

        $followups = $conversation->getPossibleFollowups($current_sequence, $current_scene);

        $this->assertTrue($followups[2]->getMessage()->getTextResponse() == 'A new list');
        $this->assertTrue($followups[6]->getMessage()->getTextResponse() == 'Clone an existing list');
        $this->assertTrue(count($followups) == 2);

        $current_sequence = 2;
        $current_scene = 'new_list';

        $followups = $conversation->getPossibleFollowups($current_sequence, $current_scene);

        $this->assertTrue($followups[3]->getMessage()->getTextResponse() == 'What do you want to call it?');
        $this->assertTrue(count($followups) == 1);

        $current_sequence = 7;
        $current_scene = 'clone_list';

        $followups = $conversation->getPossibleFollowups($current_sequence, $current_scene);

        $this->assertTrue($followups[8]->getMessage()->getTextResponse() == 'Clone the onboarding list');
        $this->assertTrue(count($followups) == 1);
    }


    private function buildConversation()
    {
        $conversation = new Conversation();

        $conversation = new Conversation();

        $conversation->createScene('init')
            ->addParticipantToScene('init', 'bot1')
            ->addParticipantToScene('init', 'bot2')
            ->addUtterance([
                'scene' => 'init',
                'sender' => 'bot1',
                'receiver' => 'bot2',
                'message' => new Message('Create list')])
            ->addUtterance([
                'scene' => 'init',
                'sender' => 'bot2',
                'receiver' => 'bot1',
                'message' => new Message('Do you want a new list or a clone')])

            // New scene for create new list dialog
            ->createScene('new_list')
            ->addParticipantToScene('new_list', 'bot1')
            ->addParticipantToScene('new_list', 'bot2')
            ->addUtterance([
                'starting_scene' => 'init',
                'ending_scene' => 'new_list',
                'sender' => 'bot1',
                'receiver' => 'bot2',
                'message' => new Message('A new list')])
            ->addUtterance([
                'scene' => 'new_list',
                'sender' => 'bot2',
                'receiver' => 'bot1',
                'message' => new Message('What do you want to call it?')])
            ->addUtterance([
                'scene' => 'new_list',
                'sender' => 'bot1',
                'receiver' => 'bot2',
                'message' => new Message('Call it MyList')])
            ->addUtterance([
                'scene' => 'new_list',
                'sender' => 'bot2',
                'receiver' => 'bot1',
                'message' => new Message('A new list has been created. All done.'),
                'completes' => true])

            // New scene for clone list dialog
            ->createScene('clone_list')
            ->addParticipantToScene('clone_list', 'bot1')
            ->addParticipantToScene('clone_list', 'bot2')
            ->addUtterance([
                'starting_scene' => 'init',
                'ending_scene' => 'clone_list',
                'sender' => 'bot1',
                'receiver' => 'bot2',
                'message' => new Message('Clone an existing list')])
            ->addUtterance([
                'scene' => 'clone_list',
                'sender'=> 'bot2',
                'receiver' => 'bot1',
                'message' => new Message('Which list should we clone')])
            ->addUtterance([
                'scene' => 'clone_list',
                'sender' => 'bot1',
                'receiver' => 'bot2',
                'message' => new Message('Clone the onboarding list')])
            ->addUtterance([
                'scene' => 'clone_list',
                'sender' => 'bot2',
                'receiver' => 'bot1',
                'message' => new Message('Onboarding list cloned')]);

        return $conversation;
    }
}
