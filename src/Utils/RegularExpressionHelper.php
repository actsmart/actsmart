<?php

namespace actsmart\actsmart\Utils;

/**
 * Trait RegularExpressionHelper
 * @package actsmart\actsmart\Utils
 *
 * Helpful regex patterns, especially for conversations in Slack.
 */
trait RegularExpressionHelper
{
    /**
     * Removes all usernames from the string.
     *
     * @param $message
     * @return null|string|string[]
     */
    public function removeAllUsernames($message)
    {
        return preg_replace("/(<@)\w+(>)/", "", $message);
    }


    /**
     * Returns true if the userName is mentioned.
     *
     * @param $message
     * @param $username
     * @return bool
     */
    public function userNameMentioned($message, $username)
    {
        return preg_match("/(<@".$username.">)/", $message);
    }

    /**
     * Returns true if a candidate from each word group is present.
     *
     * @param string $message
     * @param array $wordGroups - an array of wordGroups - as an array.
     * @return bool
     */
    public function wordsMentioned(string $message, $wordGroups = [])
    {
        $mentioned = true;
        foreach ($wordGroups as $group) {
            if (!preg_match("/" . $this->createCaptureGroup($group) . "/", $message)) {
                return false;
            }
        }
        return $mentioned;
    }

    private function createCaptureGroup($words) {
        return '(' . implode("|", $words ) . ')';
    }
}
