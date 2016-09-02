<?php

namespace Nassau\PocztaPolskaPnaBundle\Form;

use Nassau\PocztaPolskaPnaBundle\Entity\PnaCityInterface;
use Nassau\PocztaPolskaPnaBundle\Services\CityProvider\CityProviderInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\PropertyAccess\PropertyAccessor;

class NameCodeSubscriber implements EventSubscriberInterface
{
    private $fields = [
        'city'      => 'city',
        'commune'   => 'commune',
        'county'    => 'county',
        'province'  => 'province',
        'post_code' => 'post_code',
    ];

    /**
     * @var CityProviderInterface
     */
    private $cityProvider;

    /**
     * @var PropertyAccessor
     */
    private $propertyAccessor;

    /**
     * @param CityProviderInterface $cityProvider
     */
    public function __construct(CityProviderInterface $cityProvider)
    {
        $this->cityProvider = $cityProvider;
        $this->propertyAccessor = PropertyAccess::createPropertyAccessor();
    }


    public static function getSubscribedEvents()
    {
        return [
            FormEvents::PRE_SET_DATA => 'onFormPreSetData'
        ];
    }

    /**
     * @param array $fields
     *
     * @return $this
     */
    public function withFieldNames(array $fields = [])
    {
        $fields = array_replace($this->fields, $fields);

        $subscriber = clone $this;
        $subscriber->fields = $fields;

        return $subscriber;
    }

    public function onFormPreSetData(FormEvent $event)
    {
        $data = $event->getData();

        if (false === is_array($data) && false === is_object($data)) {
            return ;
        }

        $city = $this->getValue($data, 'city');
        $postCode = $this->getValue($data, 'post_code');

        if ("" === $city || "" === $postCode) {
            return;
        }

        $commune = $this->getValue($data, 'commune');
        $county = $this->getValue($data, 'county');
        $province = $this->getValue($data, 'province');
        if ("" !== $commune && "" !== $county && "" !== $province) {
            return;
        }

        $city = $this->cityProvider->findByNameAndCode($city, $postCode);

        if (false === $city instanceof PnaCityInterface) {
            // oops! we cannot fill them out!
            return ;
        }

        $data = $this->setValues($data, [
            'commune' => $city->getCommune(),
            'county' => $city->getCounty(),
            'province' => $city->getProvince(),
        ]);

        $event->setData($data);

    }

    private function getValue($data, $key)
    {
        if (false === isset($this->fields[$key])) {
            return "";
        }

        $path = $this->fields[$key];
        if (is_array($data)) {
            $path = sprintf('[%s]', $path);
        }

        return (string)$this->propertyAccessor->getValue($data, $path);
    }

    private function setValues($data, array $values)
    {
        foreach ($values as $key => $value) {
            $path = $this->fields[$key];
            if (is_array($data)) {
                $path = sprintf('[%s]', $path);
            }

            $this->propertyAccessor->setValue($data, $path, $value);
        }

        return $data;

    }
}
