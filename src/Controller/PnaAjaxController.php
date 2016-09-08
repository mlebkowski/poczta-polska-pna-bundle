<?php

namespace Nassau\PocztaPolskaPnaBundle\Controller;

use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
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

        $streetsProvider = $this->get('nassau_pna.streets_provider');

        $data = $streetsProvider->getStreets($city);

        return new JsonResponse(['streets' => array_keys($data), 'ranges' => $data]);
    }
}
