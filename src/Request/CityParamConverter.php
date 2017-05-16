<?php

namespace Nassau\PocztaPolskaPnaBundle\Request;

use Nassau\PocztaPolskaPnaBundle\DTO\City;
use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CityParamConverter implements ParamConverterInterface
{

    /**
     * Stores the object in the request.
     *
     * @param Request $request The request
     * @param ParamConverter $configuration Contains the name, class and options of the object
     *
     * @return boolean True if the object has been successfully set, else false
     */
    function apply(Request $request, ParamConverter $configuration)
    {
        $options = array_replace([
            'city' => 'city',
            'commune' => 'commune',
            'county' => 'county',
            'province' => 'province',
        ], $configuration->getOptions());

        $request->attributes->set($configuration->getName(), new City(
            $request->request->get($options['city']),
            $request->request->get($options['commune']),
            $request->request->get($options['county']),
            $request->request->get($options['province'])
        ));

        return true;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ParamConverter $configuration Should be an instance of ParamConverter
     *
     * @return boolean True if the object is supported, else false
     */
    function supports(ParamConverter $configuration)
    {
        return CityInterface::class === $configuration->getClass();
    }
}
