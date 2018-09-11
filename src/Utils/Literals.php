<?php

namespace actsmart\actsmart\Utils;


class Literals
{
    /* The utterance type */
    const TYPE = 'type';

    /* WebChat Utterance Types */
    const WEB_CHAT_MESSAGE = 'web_chat_message';
    const WEB_CHAT_ACTION = 'web_chat_action';

    /* Slack Utterance Types */
    const SLACK_MESSAGE_ACTION = 'slack_message_action';
    const SLACK_INTERACTIVE_MESSAGE = 'slack_interactive_message';
    const SLACK_DIALOG_SUBMISSION = 'slack_dialog_submission';
    const SLACK_COMMAND = 'slack_command';
    const SLACK_MESSAGE = 'slack_message';
    const UTTERANCE_EVENT = 'utterance';

    /* The text contained withing an utterance */
    const TEXT = 'text';

    /* The callback id contained within an utterance */
    const CALLBACK_ID = 'callback_id';

    /* The user id of the user the message originated from */
    const UID = 'uid';

    /* The timestamp associated with an utterance */
    const TIMESTAMP = 'timestamp';

    /* The source event this utterance was created from */
    const SOURCE_EVENT = 'source_event';

    /* */
    const WORKSPACE_ID = 'workspace_id';

    /* */
    const USER_ID = 'user_id';

    /* */
    const CHANNEL_ID = 'channel_id';

    /* */
    const ACTION = 'action';

    /* */
    const ACTION_PERFORMED_VALUE = 'action_performed_value';

    /* */
    const RESPONSE_URL = 'response_url';

    /* */
    const TOKEN = 'token';

    /* */
    const TRIGGER_ID = 'trigger_id';

    /* Identifier for an utterance */
    const UTTERANCE = 'utterance';
}