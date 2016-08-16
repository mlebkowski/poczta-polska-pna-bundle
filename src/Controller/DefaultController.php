<?php

namespace Nassau\PocztaPolskaPnaBundle\Controller;

use Nassau\PocztaPolskaPnaBundle\Validator\Constraint\PnaAddress;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{
    public function demoAction(Request $request)
    {
        $subscriber = $this->get('nassau_pna.form.name_code_subscriber');

        $data = (object)array_replace([
            'city' => '',
            'street' => '',
            'house_number' => '',
            'post_code' => '',
            'county' => '',
            'commune' => '',
            'province' => '',
        ], $request->query->get('address', []));

        $builder = $this->createFormBuilder($data, ['constraints' => new PnaAddress()])
            ->add('city')
            ->add('street')
            ->add('house_number')
            ->add('post_code')
            ->add('commune', null, ['read_only' => true])
            ->add('county', null, ['read_only' => true])
            ->add('province', null, ['read_only' => true])
            ->add('submit', 'submit', ['label' => 'Send']);

        $builder->addEventSubscriber($subscriber->withFieldNames());

        $form = $builder->getForm();

        $form->handleRequest($request);

        $index = $this->get('goldenline_algolia.client')->initIndex($this->container->getParameter('pna.index_name'));

        return $this->render('PocztaPolskaPnaBundle:Default:demo.html.twig', [
            'valid' => $form->isValid(),
            'form' => $form->createView(),
            'appId' => $index->context->applicationID,
            'apiKey' => $index->context->apiKey,
            'indexName' => $index->indexName,
        ]);
    }
}
