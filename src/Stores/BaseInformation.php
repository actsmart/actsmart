<?php

namespace actsmart\actsmart\Stores;

abstract class BaseInformation implements InformationInterface
{
    /* @var string $type */
    private $type;

    /* @var string $id */
    private $id;

    private $value;

    public function __construct($type, $id, $value) {
        $this->type = $type;
        $this->id = $id;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }




}