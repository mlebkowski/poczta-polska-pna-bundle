<?php

namespace Nassau\PocztaPolskaPnaBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class PnaCity implements PnaCityInterface
{

    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $commune;

    /**
     * @var string
     */
    private $county;

    /**
     * @var string
     */
    private $province;

    /**
     * @var string
     */
    private $code;

    /**
     * @var PnaStreet[]|Collection
     */
    private $streets;

    /**
     */
    public function __construct()
    {
        $this->streets = new ArrayCollection();
    }


    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    public function addStreet(PnaStreet $street)
    {
        $this->code = null;

        $this->streets->add($street->setCity($this));

        return $this;
    }

    /**
     * @return PnaStreet[]|Collection
     */
    public function getStreets()
    {
        return $this->streets;
    }


    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getCommune()
    {
        return $this->commune;
    }

    /**
     * @param string $commune
     *
     * @return $this
     */
    public function setCommune($commune)
    {
        $this->commune = $commune;

        return $this;
    }

    /**
     * @return string
     */
    public function getCounty()
    {
        return $this->county;
    }

    /**
     * @param string $county
     *
     * @return $this
     */
    public function setCounty($county)
    {
        $this->county = $county;

        return $this;
    }

    /**
     * @return string
     */
    public function getProvince()
    {
        return $this->province;
    }

    /**
     * @param string $province
     *
     * @return $this
     */
    public function setProvince($province)
    {
        $this->province = $province;

        return $this;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    public function getUniqueName()
    {
        return sprintf('%s, gm. %s, pow. %s, woj. %s', $this->name, $this->commune, $this->county, $this->province);
    }

}
