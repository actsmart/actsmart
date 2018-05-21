<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * Buttons are common to some message types and elements.
 *
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages/buttons
 */
trait HasButtons
{
    protected $buttons = [];

    /**
     * Adds a button with a standard web link that opens in the messenger web view.
     * URLs used here must be whitelisted for the Facebook Page linked to the app sending the message
     *
     * @see https://developers.facebook.com/docs/messenger-platform/reference/buttons/url
     *
     * URL whitelisting:
     * https://developers.facebook.com/docs/messenger-platform/reference/messenger-profile-api/domain-whitelisting
     *
     * @param $title
     * @param $url
     * @return $this
     * @throws \Exception
     */
    public function addWebUrlButton($title, $url)
    {
        $this->addButton([
            'type' => 'web_url',
            'url' => $url,
            'title' => $title,
            'messenger_extensions' => true
        ]);

        return $this;
    }

    /**
     * Buttons that cause the messenger to send a message back with the payload provided
     *
     * @param $title
     * @param $postback
     *
     * @see https://developers.facebook.com/docs/messenger-platform/reference/buttons/postback
     *
     * @return $this
     * @throws \Exception
     */
    public function addPostbackButton($title, $postback)
    {
        $this->addButton([
            'type' => 'postback',
            'payload' => $postback,
            'title' => $title
        ]);

        return $this;
    }

    /**
     * Adds a button with a link to dial a phone number
     *
     * @see https://developers.facebook.com/docs/messenger-platform/reference/buttons/call/
     *
     * @param $title
     * @param $phoneNumber
     * @return $this
     * @throws \Exception
     */
    public function addCallButton($title, $phoneNumber)
    {
        $this->addButton([
            'type' => 'phone_number',
            'payload' => $phoneNumber,
            'title' => $title
        ]);

        return $this;
    }


    /*
     {
      "type": "element_share",
      "share_contents": {
        "attachment": {
          "type": "template",
          "payload": {
            "template_type": "generic",
            "elements": [
              {
                "title": "<TEMPLATE_TITLE>",
                "subtitle": "<TEMPLATE_SUBTITLE>",
                "image_url": "<IMAGE_URL_TO_DISPLAY>",
                "default_action": {
                  "type": "web_url",
                  "url": "<WEB_URL>"
                },
                "buttons": [
                  {
                    "type": "web_url",
                    "url": "<BUTTON_URL>",
                    "title": "<BUTTON_TITLE>"
                  }
                ]
              }
            ]
          }
        }
      }
    }
     */

    /**
     * Adds a share button
     *
     * @see https://developers.facebook.com/docs/messenger-platform/reference/buttons/share
     *
     * @param $title string
     * @param $subtitle string
     * @param $imageUrl string Image to show in the share
     * @param $webUrl string Used as the default url and the url for the button
     * @param $buttonTitle string Shown on the button in the share message that is sent
     *
     * // TODO this can support more buttons if we want it to
     *
     * @return $this
     * @throws \Exception
     */
    public function addShareButton($title, $subtitle, $imageUrl, $webUrl, $buttonTitle)
    {
        $this->addButton([
                'type' => 'element_share',
                'share_contents' => [
                    'attachment' => [
                        'type' => 'template',
                        'payload' => [
                            'template_type' => 'generic',
                            'elements' => [
                                [
                                    'title' => $title,
                                    'subtitle' => $subtitle,
                                    'image_url' => $imageUrl,
                                    'default_action' => [
                                        'type' => 'web_url',
                                        'url' => $webUrl
                                    ],
                                    'buttons' => [
                                        [
                                            'type' => 'web_url',
                                            'url' => $webUrl,
                                            'title' => $buttonTitle
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $this;
    }

    /**
     * @param array $button
     * @throws \Exception
     */
    private function addButton(array $button)
    {
        if (count($this->buttons) == 3) {
            throw new \Exception('Message elements can only have a maximum of 3 buttons');
        } else {
            $this->buttons[] = $button;
        }
    }
}
