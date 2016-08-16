<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

use Doctrine\Common\Collections\Collection;

interface PnaCityInterface extends CityInterface
{
    /**
     * @return string|null
     */
    public function getCode();

    /**
     * @return PnaStreetInterface[]|Collection
     */
    public function getStreets();
}
