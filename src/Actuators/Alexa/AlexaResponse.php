<?php

namespace actsmart\actsmart\Actuators\Alexa;

interface AlexaResponse
{
    const RESPONSE = 'response';

    const DIRECTIVES = 'directives';

    const TYPE = 'type';

    const OUTPUT_SPEECH = 'outputSpeech';
    const PLAIN_TEXT = 'PlainText';
    const TEXT = 'text';
    const SSML = 'SSML';
}
