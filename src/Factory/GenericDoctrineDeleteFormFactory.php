<?php

namespace Keet\Form\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractForm;
use Keet\Form\Form\GenericDoctrineDeleteForm;
use Keet\Form\InputFilter\GenericDoctrineDeleteFormInputFilter;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class GenericDoctrineDeleteFormFactory extends AbstractDoctrineFormFactory
{
    public function __construct()
    {
        parent::__construct(GenericDoctrineDeleteForm::class, GenericDoctrineDeleteFormInputFilter::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return GenericDoctrineDeleteForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractForm
    {
        if ( ! $options['entity_name']) {

            throw new ServiceNotCreatedException(
                'Option "entity_name" (FQCN) required for ' . __CLASS__ . '; not set.'
            );
        }
        if ( ! $options['unique_property']) {

            throw new ServiceNotCreatedException('Option "unique_property" required for ' . __CLASS__ . '; not set.');
        }

        $this->setObjectManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        /** @var EntityRepository $objectRepository */
        $objectRepository = $this->getObjectManager()
                                 ->getRepository($options['entity_name']);

        $form = new GenericDoctrineDeleteForm($this->name, $this->options);
        $form->setHydrator(new DoctrineObject($this->objectManager));
        $form->setObject(new $options['entity_name']());
        $form->setInputFilter(
            $this->getInputFilterPluginManager()
                 ->get(
                     GenericDoctrineDeleteFormInputFilter::class,
                     [
                         'object_manager'    => $this->getObjectManager(),
                         'object_repository' => $objectRepository,
                         'translator'        => $this->getTranslator(),
                     ]
                 )
        );
        $form->setObjectManager($this->getObjectManager());
        $form->setTranslator($this->getTranslator());
        $form->setOption('unique_property', $options['unique_property']);

        return $form;
    }
}