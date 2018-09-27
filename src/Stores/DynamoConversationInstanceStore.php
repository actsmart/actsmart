<?php

namespace actsmart\actsmart\Stores;

use actsmart\actsmart\Conversations\Slack\ConversationInstance;
use actsmart\actsmart\Utils\ComponentInterface;
use actsmart\actsmart\Utils\ComponentTrait;
use Aws\DynamoDb\DynamoDbClient;

class DynamoConversationInstanceStore extends BaseStore implements ComponentInterface, StoreInterface
{
    use ComponentTrait;

    private $client;

    private $table_name;

    public function __construct($aws_access_key, $aws_secret_access_key, $region, $table_name)
    {
        $this->client = DynamoDbClient::factory([
            'credentials' => [
                'key'    => $aws_access_key,
                'secret' => $aws_secret_access_key,
                ],
            'region' => $region
        ]);

        $this->table_name = $table_name;
    }

    public function save(ConversationInstance $ci)
    {
        $ci_record = [
            'update_ts' => ['N' => $ci->getUpdateTs()],
            'user_id' => ['S' => $ci->getUserId()],
            'conversation_template_id' => ['S' => $ci->getConversationTemplateId()],
            'current_scene_id' => ['S' => $ci->getCurrentSceneId()],
            'current_utterance_sequence_id' => ['N' => $ci->getCurrentUtteranceSequenceId()],
            'start_ts' => ['N' => $ci->getStartTs()],
            'workspace_id' => ['S' => $ci->getWorkspaceId()],
            'channel_id' => ['S' => $ci->getChannelId()]
        ];

        try {
            $this->client->putItem([
                'TableName' => $this->table_name,
                'Item' => $ci_record,
            ]);
        } catch (\Exception $e) {
            ($e->getMessage());
            return false;
        }
    }

    public function retrieve(ConversationInstance $ci)
    {
        try {
            $iterator = $this->client->getIterator('Query', [
                'TableName' => $this->table_name,
                'KeyConditions' => [
                    'update_ts' => [
                        'AttributeValueList' => [
                            ['N' => strtotime("-20 minutes")]
                        ],
                        'ComparisonOperator' => 'GT'
                    ],
                    'user_id' => [
                        'AttributeValueList' => [
                            ['S' => $ci->getUserId()]
                        ],
                        'ComparisonOperator' => 'EQ'
                    ],
                ],
                'Query-Filter' => [
                    'workspace_id' => [
                        'AttributeValueList' => [
                            ['S' => $ci->getWorkspaceId()]
                        ],
                        'ComparisonOperator' => 'EQ'
                    ],
                    'channel_id' => [
                        'AttributeValueList' => [
                            ['S' => $ci->getChannelId()]
                        ],
                        'ComparisonOperator' => 'EQ'
                    ],
                ]
            ]);
        } catch (\Exception $e) {
            //$e->getMessage();
            return false;
        }

        // Each item will contain the attributes we added
        foreach ($iterator as $item) {
            if ($item) {
                $ci->setConversationTemplateId($item['conversation_template_id']['S'])
                    ->setCurrentUtteranceSequenceId($item['current_utterance_sequence_id']['N'])
                    ->setCurrentSceneId($item['current_scene_id']['S'])
                    ->setStartTs($item['start_ts']['N'])
                    ->setUpdateTs($item['update_ts']['N']);
                // Thaw the conversation object itself
                $ci->setConversation();
                return $ci;
            }
        }

        return false;
    }

    public function delete(ConversationInstance $ci)
    {
        try {
            $this->client->deleteItem(array(
                'TableName' => $this->table_name,
                'Key' => array(
                    'update_ts' => array('N' => $ci->getUpdateTs()),
                    'user_id' => array('S' => $ci->getUserId())
                )
            ));
        } catch (\Exception $e) {
            $this->logger->error(sprintf("Error deleting conversation ", $e->getMessage()));
        }
    }

    public function update(ConversationInstance $ci)
    {
        // Not sure we need an update.
    }

    public function getKey()
    {
        return 'store.conversation_instance';
    }
}
