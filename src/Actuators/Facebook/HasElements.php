<?php

namespace actsmart\actsmart\Actuators\Facebook;

trait HasElements
{
    private $elements;

    /**
     * Returns all elements
     *
     * @return mixed
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Adds an element to the list
     *
     * @param FacebookElement $element
     */
    public function addElement(FacebookElement $element)
    {
        $this->elements[] = $element;
    }
}
