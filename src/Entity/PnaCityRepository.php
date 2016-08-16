<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

use Doctrine\ORM\EntityRepository;

class PnaCityRepository extends EntityRepository
{
    /**
     * @param CityInterface $city
     *
     * @return PnaCity|null
     */
    public function findOneByCity(CityInterface $city)
    {
        return $this->findOneBy([
            'name'     => $city->getName(),
            'commune'  => $city->getCommune(),
            'county'   => $city->getCounty(),
            'province' => $city->getProvince(),
        ]);
    }
}
