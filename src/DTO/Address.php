<?php

namespace Nassau\PocztaPolskaPnaBundle\DTO;

class Address extends City
{
    private $street;
    private $houseNumber;
    private $postCode;

    /**
     * @param string $city
     * @param string $commune
     * @param string $county
     * @param string $province
     * @param string $street
     * @param string $houseNumber
     * @param string $postCode
     */
    public function __construct($city, $commune, $county, $province, $street, $houseNumber, $postCode)
    {
        parent::__construct($city, $commune, $county, $province);
        $this->street = $street;
        $this->houseNumber = $houseNumber;
        $this->postCode = $postCode;
    }


    /**
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * @return string
     */
    public function getHouseNumber()
    {
        return $this->houseNumber;
    }

    /**
     * @return string
     */
    public function getPostCode()
    {
        return $this->postCode;
    }

}
