<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

interface PnaStreetInterface
{
    /**
     * @return string|null
     */
    public function getName();

    /**
     * @return string
     */
    public function getRanges();

    /**
     * @return string
     */
    public function getCode();
}
