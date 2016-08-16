<?php

namespace Nassau\PocztaPolskaPnaBundle\Controller;

use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaStreet;
use Nassau\PocztaPolskaRanges\RangeDefinition;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PnaAjaxController extends Controller
{
    /**
     * @param CityInterface $city
     * @ParamConverter()
     *
     * @return JsonResponse
     */
    public function streetsAction(CityInterface $city)
    {
        $city = $this->get('nassau_pna.city_provider')->findCity($city);

        if (false === $city instanceof PnaCityInterface) {
            throw new NotFoundHttpException;
        }

        $rangesParser = $this->get('nassau_pna.ranges_parser');

        $rangesCallback = function ($ranges) use ($rangesParser) {
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
            }, $rangesParser->parse($ranges));
        };

        $data = array_map(function (PnaStreet $street) use ($rangesCallback) {
            return [
                'name'   => $street->getName(),
                'ranges' => $rangesCallback($street->getRanges()),
                'code'   => $street->getCode(),
            ];
        }, iterator_to_array($city->getStreets()));

        $data = array_reduce($data, function ($data, $street) {
            $data[$street['name']][] = ['code' => $street['code'], 'ranges' => $street['ranges']];

            return $data;
        }, []);

        return new JsonResponse(['streets' => array_keys($data), 'ranges' => $data]);
    }
}
