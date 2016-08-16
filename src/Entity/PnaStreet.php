<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

class PnaStreet implements PnaStreetInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var PnaCity
     */
    private $city;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $ranges;

    /**
     * @param string $name
     * @param string $code
     * @param string $ranges
     */
    public function __construct($name, $code, $ranges = null)
    {
        $this->name = $name;
        $this->code = $code;
        $this->ranges = $ranges ?: null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param PnaCity $city
     *
     * @return $this
     */
    public function setCity(PnaCity $city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getRanges()
    {
        return $this->ranges ?: "";
    }

}
