<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\StreetsProvider;

use Doctrine\Common\Cache\Cache;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;

class CachedStreetsProvider implements StreetsProviderInterface
{

    /**
     * @var Cache
     */
    private $cache;

    /**
     * @var StreetsProviderInterface
     */
    private $innerProvider;

    /**
     * @param Cache $cache
     * @param StreetsProviderInterface $innerProvider
     */
    public function __construct(Cache $cache, StreetsProviderInterface $innerProvider)
    {
        $this->cache = $cache;
        $this->innerProvider = $innerProvider;
    }


    public function getStreets(PnaCityInterface $city)
    {
        $key = sprintf("%s_%s_%s_%s", $city->getName(), $city->getCommune(), $city->getCounty(), $city->getProvince());

        if ($this->cache->contains($key)) {
            return $this->cache->fetch($key);
        }

        $data = $this->innerProvider->getStreets($city);

        $this->cache->save($key, $data);

        return $data;
    }
}
