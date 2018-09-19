<?php

namespace actsmart\actsmart\Utils;


class Literals
{
    /* The utterance type */
    const TYPE = 'type';

    /* WebChat Utterance Types */
    const WEB_CHAT_MESSAGE = 'web_chat_message';
    const WEB_CHAT_ACTION = 'web_chat_action';

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

    /* Identifier for an utterance */
    const UTTERANCE = 'utterance';

    /* NLP Interpreters */
    const GOOGLE_CLOUD_NLP = 'interpreter.nlp.google_nlp';
}