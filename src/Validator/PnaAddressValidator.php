<?php

namespace Nassau\PocztaPolskaPnaBundle\Validator;

use Nassau\PocztaPolskaPnaBundle\DTO\Address;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaStreetInterface;
use Nassau\PocztaPolskaPnaBundle\Services\CityProvider\CityProviderInterface;
use Nassau\PocztaPolskaPnaBundle\Validator\Constraint\PnaAddress;
use Nassau\PocztaPolskaRanges\RangeChecker;
use Nassau\PocztaPolskaRanges\RangeParserException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class PnaAddressValidator extends ConstraintValidator
{
    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @var CityProviderInterface
     */
    private $cityProvider;

    /**
     * @var RangeChecker
     */
    private $rangeChecker;

    public function __construct(CityProviderInterface $cityProvider, RangeChecker $rangeChecker)
    {
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
        $this->cityProvider = $cityProvider;
        $this->rangeChecker = $rangeChecker;
    }

    /**
     * @param mixed                 $value
     * @param PnaAddress|Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $address = $this->createAddress($value, $constraint);

        $pnaCity = $this->cityProvider->findCity($address);

        if (false === $pnaCity instanceof PnaCityInterface) {
            if ("londyn" === strtolower($address->getCity())) {
                $this->addViolation('Nie ma takiego miasta Londyn… Jest Lądek, Lądek Zdrój', $constraint->cityPath);
            } else {
                $this->addViolation('pna.no_city', $constraint->cityPath);
            }

            return;
        }

        if ($pnaCity->getCode()) {
            if ($address->getPostCode() !== $pnaCity->getCode()) {
                $this->addViolation('pna.post_code_mismatch', $constraint->postCodePath);
            }

            return;
        }

        $hasStreets = $pnaCity->getStreets()->exists(function ($key, PnaStreetInterface $street) {
            return "" !== $street->getName() && !($key xor $key);
        });

        $matchingStreets = $pnaCity->getStreets()->filter(function (PnaStreetInterface $street) use ($address, $hasStreets) {
            return false === $hasStreets || strtolower($street->getName()) === strtolower(trim($address->getStreet()));
        });

        if (0 === $matchingStreets->count()) {
            $this->addViolation('pna.invalid_street', $constraint->streetPath);

            return;
        }

        $expectedCodes = $matchingStreets->filter(function (PnaStreetInterface $street) use ($address) {
            try {
                return $this->rangeChecker->isInRanges($address->getHouseNumber(), $street->getRanges());
            } catch (RangeParserException $e) {
                return false;
            }
        })->map(function (PnaStreetInterface $street) {
            return $street->getCode();
        })->toArray();

        if (0 === sizeof($expectedCodes)) {
            $this->addViolation('pna.invalid_house_number', $constraint->houseNumberPath);

            return;
        }

        if (false === in_array($address->getPostCode(), $expectedCodes, true)) {
            $this->addViolation('pna.street_post_code_mismatch', $constraint->postCodePath);

            return;
        }
    }

    private function addViolation($message, $path)
    {
        $this->buildViolation($message)->atPath($path)->addViolation();
    }


    private function createAddress($value, PnaAddress $constraint)
    {
        if (is_array($value)) {
            $value = (object)$value;
        }

        return new Address(
            $this->propertyAccessor->getValue($value, $constraint->cityPath),
            $this->propertyAccessor->getValue($value, $constraint->communePath),
            $this->propertyAccessor->getValue($value, $constraint->countyPath),
            $this->propertyAccessor->getValue($value, $constraint->provincePath),
            $this->propertyAccessor->getValue($value, $constraint->streetPath),
            $this->propertyAccessor->getValue($value, $constraint->houseNumberPath),
            $this->propertyAccessor->getValue($value, $constraint->postCodePath)
        );
    }
}
