<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\CityProvider;

use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityRepository;

class DoctrineCityProvider implements CityProviderInterface
{

    /**
     * @var PnaCityRepository
     */
    private $repository;

    /**
     * @param PnaCityRepository $repository
     */
    public function __construct(PnaCityRepository $repository)
    {
        $this->repository = $repository;
    }


    /**
     * @param CityInterface $city
     *
     * @return PnaCityInterface
     */
    public function findCity(CityInterface $city)
    {
        return $this->repository->findOneByCity($city);
    }

    /**
     * @param string $name
     * @param string $code
     *
     * @return PnaCityInterface
     */
    public function findByNameAndCode($name, $code)
    {
        return $this->repository->createQueryBuilder('city')
            ->leftJoin('city.streets', 'street')
            ->where('city.name = :name')
            ->setParameter('name', $name)
            ->andWhere('street.code = :code OR city.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
