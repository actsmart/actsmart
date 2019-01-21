<?php

namespace actsmart\actsmart\Utils;

class Literals
{
    /* The utterance type */
    const TYPE = 'type';

    /* WebChat Utterance Types */
    const WEB_CHAT_MESSAGE = 'web_chat_message';
    const WEB_CHAT_ACTION = 'web_chat_action';
    const WEB_CHAT_FORM = 'web_chat_form';
    const WEB_CHAT_OPEN = 'web_chat_open';
    const WEB_CHAT_IMAGE = 'web_chat_image';
    const WEB_CHAT_LIST = 'web_chat_list';
    const WEB_CHAT_URL_CLICK = 'web_chat_url_click';
    const WEB_CHAT_TRIGGER = 'web_chat_trigger';

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

    /* The data object contained within an utterance */
    const DATA = 'data';

    /* The callback id contained within an utterance */
    const CALLBACK_ID = 'callback_id';

    /* The callback data contained within an utterance */
    const CALLBACK_DATA = 'callback_data';

    /* The value associated to an action performed */
    const VALUE = 'value';

    const URL = 'url';

    /* The timestamp associated with an utterance */
    const TIMESTAMP = 'timestamp';

    /* The source event this utterance was created from */
    const SOURCE_EVENT = 'source_event';

    /* The workspace id */
    const WORKSPACE_ID = 'workspace_id';

    /* The user */
    const USER = 'user';

    /* The user id */
    const USER_ID = 'user_id';

    const LOGGED_IN_USER = 'logged_in_user';

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

    const FORM_VALUES = 'form_values';

    /* The dialog submission */
    const SUBMISSION = 'submission';

    /* Identifier for an utterance */
    const UTTERANCE = 'utterance';

    /* NLP Interpreters */
    const GOOGLE_CLOUD_NLP = 'interpreter.nlp.google_nlp';

    /* Stores */
    const CONTEXT_STORE = 'store.context';

    /* The language used in an utterance */
    const LANGUAGE = 'language';

}
