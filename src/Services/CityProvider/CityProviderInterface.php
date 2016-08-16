<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\CityProvider;

use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;

interface CityProviderInterface
{
    /**
     * @param CityInterface $city
     *
     * @return PnaCityInterface
     */
    public function findCity(CityInterface $city);

    /**
     * @param $name
     * @param $code
     *
     * @return PnaCityInterface
     */
    public function findByNameAndCode($name, $code);
}
