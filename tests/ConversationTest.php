<?php

namespace actsmart\actsmart\Tests;

use actsmart\actsmart\Conversations\Conversation;
use \Fhaculty\Graph\Graph as Graph;
use \Fhaculty\Graph\Vertex;
use Fhaculty\Graph\Set\Vertices;

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

        $this->assertArrayHasKey('init', $conversation->getScenes());

        $vertex = $conversation->getScene('init');
        $this->assertTrue($vertex->getId() == 'init');
    }

    public function testParticipantAddition()
    {
        $conversation = new Conversation();

        /* @var \FHaculty\Graph\Vertex $scene */
        $scene = $conversation->createScene('init');

        /* @var \FHaculty\Graph\Vertex $participant */
        $participant1 = $conversation->addParticipantToScene('init', 'bot1');
        $participant2 = $conversation->addParticipantToScene('init', 'bot2');

        // The participant should have an edge leading from the scene
        $this->assertTrue($participant1->hasEdgeFrom($scene));
        $this->assertTrue($participant2->hasEdgeFrom($scene));

        // Retrieve both participants starting from the Scene
        $participants = $scene->getVerticesEdgeTo();

        $this->assertTrue($participants->hasVertexId('init' . '_' . 'bot1'));
        $this->assertTrue($participants->hasVertexId('init' . '_' . 'bot2'));
    }

    /**
     * @group focus
     */
    public function testUtteranceAddition()
    {
        $conversation = new Conversation();

        /* @var \FHaculty\Graph\Vertex $scene */
        $scene = $conversation->createScene('init');

        /* @var \FHaculty\Graph\Vertex $participant */
        $participant1 = $conversation->addParticipantToScene('init', 'bot1');
        $participant2 = $conversation->addParticipantToScene('init', 'bot2');

        $edge = $conversation->addUtterance('init', 'bot1', 'bot2', 'How is it going');

        $this->assertTrue($edge->isConnection($participant1, $participant2));

    }


}
