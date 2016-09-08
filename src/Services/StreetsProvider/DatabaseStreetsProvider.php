<?php

namespace Nassau\PocztaPolskaPnaBundle\Services\StreetsProvider;

use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaStreetInterface;
use Nassau\PocztaPolskaRanges\RangeDefinition;
use Nassau\PocztaPolskaRanges\RangesParser;

class DatabaseStreetsProvider implements StreetsProviderInterface
{
    /**
     * @var RangesParser
     */
    private $rangesParser;

    /**
     * @param RangesParser $rangesParser
     */
    public function __construct(RangesParser $rangesParser)
    {
        $this->rangesParser = $rangesParser;
    }


    public function getStreets(PnaCityInterface $city)
    {
        $rangesCallback = function ($ranges) {
            if (!$ranges) {
                return null;
            }

            return array_map(function (RangeDefinition $definition) {
                return [
                    'from' => $definition->getFrom(),
                    'to' => $definition->getTo(),
                    'only_odd' => $definition->getParity() === $definition::PARITY_ODD,
                    'only_even' => $definition->getParity() === $definition::PARITY_EVEN,
                ];
            }, $this->rangesParser->parse($ranges));
        };

        $data = array_map(function (PnaStreetInterface $street) use ($rangesCallback) {
            return [
                'name' => $street->getName(),
                'ranges' => $rangesCallback($street->getRanges()),
                'code' => $street->getCode(),
            ];
        }, iterator_to_array($city->getStreets()));

        $data = array_reduce($data, function ($data, $street) {
            $data[$street['name']][] = ['code' => $street['code'], 'ranges' => $street['ranges']];

            return $data;
        }, []);

        return $data;
    }
}
