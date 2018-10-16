<?php

namespace actsmart\actsmart\Utils;


class Literals
{
    /* The utterance type */
    const TYPE = 'type';

    /* WebChat Utterance Types */
    const WEB_CHAT_MESSAGE = 'web_chat_message';
    const WEB_CHAT_ACTION = 'web_chat_action';
    const WEB_CHAT_OPEN = 'web_chat_open';

    /* Slack Utterance Types */
    const SLACK_MESSAGE_ACTION = 'slack_message_action';
    const SLACK_INTERACTIVE_MESSAGE = 'slack_interactive_message';
    const SLACK_DIALOG_SUBMISSION = 'slack_dialog_submission';
    const SLACK_COMMAND = 'slack_command';
    const SLACK_MESSAGE = 'slack_message';

    /* General identifier for message */
    const MESSAGE = 'message';

    /* The text contained withing an utterance */
    const TEXT = 'text';

    /* The callback id contained within an utterance */
    const CALLBACK_ID = 'callback_id';

    /* The callback data contained within an utterance */
    const CALLBACK_DATA = 'callback_data';

    /* The user id of the user the message originated from */
    const UID = 'uid';

    /* The timestamp associated with an utterance */
    const TIMESTAMP = 'timestamp';

    /* The source event this utterance was created from */
    const SOURCE_EVENT = 'source_event';

    /* The workspace id */
    const WORKSPACE_ID = 'workspace_id';

    /* The user id */
    const USER_ID = 'user_id';

    /* The channel id */
    const CHANNEL_ID = 'channel_id';

    /* The item id */
    const ITEM_ID = 'item_id';

    /* The action */
    const ACTION = 'action';

    /* The action performed value */
    const ACTION_PERFORMED_VALUE = 'action_performed_value';

    /* The response url */
    const RESPONSE_URL = 'response_url';

    /* The token */
    const TOKEN = 'token';

    /* The trigger id */
    const TRIGGER_ID = 'trigger_id';

    /* The attachments */
    const ATTACHMENTS = 'attachments';

    /* The dialog submission */
    const SUBMISSION = 'submission';

    /* Identifier for an utterance */
    const UTTERANCE = 'utterance';


    /* NLP Interpreters */
    const GOOGLE_CLOUD_NLP = 'interpreter.nlp.google_nlp';

}