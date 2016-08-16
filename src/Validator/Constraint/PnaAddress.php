<?php

namespace Nassau\PocztaPolskaPnaBundle\Validator\Constraint;

use Nassau\PocztaPolskaPnaBundle\Validator\PnaAddressValidator;
use Symfony\Component\Validator\Constraint;

class PnaAddress extends Constraint
{
    public $cityPath = 'city';
    public $communePath = 'commune';
    public $countyPath = 'county';
    public $provincePath = 'province';
    public $postCodePath = 'post_code';
    public $streetPath = 'street';
    public $houseNumberPath = 'house_number';

    /**
     * @inheritDoc
     */
    public function validatedBy()
    {
        return PnaAddressValidator::class;
    }

    /**
     * @inheritDoc
     */
    public function getTargets()
    {
        return self::CLASS_CONSTRAINT;
    }

}
