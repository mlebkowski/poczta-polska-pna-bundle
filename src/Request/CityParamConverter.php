<?php

namespace Nassau\PocztaPolskaPnaBundle\Request;

use Nassau\PocztaPolskaPnaBundle\DTO\City;
use Nassau\PocztaPolskaPnaBundle\Entity\CityInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ConfigurationInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;

class CityParamConverter implements ParamConverterInterface
{

    /**
     * Stores the object in the request.
     *
     * @param Request                $request       The request
     * @param ConfigurationInterface $configuration Contains the name, class and options of the object
     *
     * @return boolean True if the object has been successfully set, else false
     */
    function apply(Request $request, ConfigurationInterface $configuration)
    {
        if ($configuration instanceof ParamConverter) {
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

        return false;
    }

    /**
     * Checks if the object is supported.
     *
     * @param ConfigurationInterface $configuration Should be an instance of ParamConverter
     *
     * @return boolean True if the object is supported, else false
     */
    function supports(ConfigurationInterface $configuration)
    {
        if ($configuration instanceof ParamConverter) {
            return CityInterface::class === $configuration->getClass();
        }

        return false;
    }
}
