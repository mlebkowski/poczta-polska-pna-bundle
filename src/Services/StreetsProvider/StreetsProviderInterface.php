<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\StreetsProvider;

use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;

interface StreetsProviderInterface
{
    /**
     * Returns streets divided into ranges for given city
     *
     * @param PnaCityInterface $city
     * @return array
     */
    public function getStreets(PnaCityInterface $city);
}
