<?php

namespace actsmart\actsmart\Conversations\WebChat;

interface WebChatResponseInterface
{
    public function getWebChatResponse($actionData = null, $informationResponse = null);
}
