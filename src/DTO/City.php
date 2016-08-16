<?php

namespace Nassau\PocztaPolskaPnaBundle\DTO;

use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;

class City implements CityInterface
{
    private $city;

    private $commune;

    private $county;

    private $province;

    /**
     * @param string $province
     * @param string $city
     * @param string $commune
     * @param string $county
     */
    public function __construct($city, $commune, $county, $province)
    {
        $this->city = $city;
        $this->commune = $commune;
        $this->county = $county;
        $this->province = $province;
    }

    /**
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    public function getName()
    {
        return $this->getCity();
    }

    /**
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }
}
