<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * An element that can be added to a template message
 *
 */
class FacebookElement implements \JsonSerializable
{
    use HasButtons;

    private $title;
    private $imageUrl;
    private $subtitle;

    private $defaultAction;

    const WEB_URL = 'web_url';
    const POSTBACK = 'postback';

    /**
     * @param mixed $title
     * @return $this
     */
    public function setTitle($title): FacebookElement
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @param mixed $imageUrl
     * @return $this
     */
    public function setImageUrl($imageUrl): FacebookElement
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    /**
     * @param mixed $subtitle
     * @return $this
     */
    public function setSubtitle($subtitle): FacebookElement
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Sets the default action for the element.
     * At the moment, we are just supporting web_urls. This can be customised a lot more
     * @see https://developers.facebook.com/docs/messenger-platform/reference/template/generic
     *
     * @param $url string The url to link to
     * @param $fallbackUrl string The fallback url to use where messenger extensions are not supported
     * @param string $height The height of the webview link. 1 of tall, compact or full
     * @return $this
     */
    public function setDefaultAction($url, $height = 'tall')
    {
        $this->defaultAction = [
            'type'                 => self::WEB_URL,
            'url'                  => $url,
            'messenger_extensions' => false,
            'webview_height_ratio' => $height
        ];

        return $this;
    }

    /**
     * How an element should be serialised for sending
     *
     * @return array|mixed
     */
    public function jsonSerialize()
    {
        return [
            "title"          => $this->title,
            "image_url"      => $this->imageUrl,
            "subtitle"       => $this->subtitle,
            "default_action" => $this->defaultAction,
            "buttons"        => $this->buttons
        ];
    }
}
