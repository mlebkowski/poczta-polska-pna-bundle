<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

interface CityInterface
{
    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getCommune();

    /**
     * @return string
     */
    public function getCounty();

    /**
     * @return string
     */
    public function getProvince();
}
