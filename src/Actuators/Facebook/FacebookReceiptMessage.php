<?php

namespace actsmart\actsmart\Actuators\Facebook;

/**
 * @see https://developers.facebook.com/docs/messenger-platform/send-messages/template/receipt
 */
class FacebookReceiptMessage extends FacebookMessage
{
    /**
     * These are receipt specific elements
     * @var array
     */
    private $elements = [];

    private $recipientName;

    private $orderNumber;

    private $currency;

    private $paymentMethod;

    private $orderUrl;

    private $timestamp;

    private $address;

    private $summary;

    public function __construct($userId)
    {
        parent::__construct($userId);

        $this->timestamp = (new \DateTime())->getTimestamp();
    }

    public function addElement($title, $subtitle, $quantity, $price, $currency, $imageUrl)
    {
        $this->elements[] = [
            'title' => $title,
            'subtitle' => $subtitle,
            'quantity' => $quantity,
            'price' => $price,
            'currency' => $currency,
            'image_url' => $imageUrl
        ];
    }

    /**
     * Sets the shipping address
     *
     * @param $street1
     * @param $street2
     * @param $city
     * @param $postalCode
     * @param string $country
     */
    public function setAddress($street1, $street2, $city,  $postalCode, $state, $country = 'UK')
    {
        $this->address = [
            'street_1' => $street1,
            'street_2' => $street2,
            'city' => $city,
            'postal_code' => $postalCode,
            'state' => $state,
            'country' => $country
        ];
    }

    public function setSummary($subtotal, $shippingCost, $totalTax, $totalCost)
    {
        $this->summary = [
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_tax' => $totalTax,
            'total_cost' => $totalCost
        ];
    }

    /**
     * @param mixed $recipientName
     */
    public function setRecipientName($recipientName): void
    {
        $this->recipientName = $recipientName;
    }

    /**
     * @param mixed $orderNumber
     */
    public function setOrderNumber($orderNumber): void
    {
        $this->orderNumber = $orderNumber;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param mixed $paymentMethod
     */
    public function setPaymentMethod($paymentMethod): void
    {
        $this->paymentMethod = $paymentMethod;
    }

    /**
     * @param mixed $orderUrl
     */
    public function setOrderUrl($orderUrl): void
    {
        $this->orderUrl = $orderUrl;
    }

    /**
     * Adds the quick replies to the message to post
     *
     * @return array
     */
    public function getMessageToPost()
    {
        $message = parent::getMessageToPost();
        $message['message']['attachment'] = [
            'type' => 'template',
            'payload' => [
                "template_type" => "receipt",
                'recipient_name' => $this->recipientName,
                'order_number' => $this->orderNumber,
                'currency' => $this->currency,
                'payment_method' => $this->paymentMethod,
                'order_url' => $this->orderUrl,
                'timestamp' => $this->timestamp,
                'address' => $this->address,
                'summary' => $this->summary,
                'elements' => $this->elements
            ]
        ];

        return $message;
    }
}
