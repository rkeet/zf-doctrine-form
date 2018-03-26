<?php

namespace Keet\Form\Factory;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Keet\Form\Form\GenericDoctrineDeleteForm;
use Zend\ServiceManager\Exception\ServiceNotCreatedException;

class GenericDoctrineDeleteFormFactory extends AbstractDoctrineFormFactory
{
    public function __construct()
    {
        parent::__construct(GenericDoctrineDeleteForm::class, GenericDoctrineDeleteFieldsetInputFilter::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GenericDoctrineDeleteForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        if (!$this->entityName) {

            throw new ServiceNotCreatedException('Entity name (entityName (FQCN)) required for GenericDeleteFormFactory not set.');
        }

        $this->setObjectManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('translator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        /** @var EntityRepository $entityRepository */
        $entityRepository = $this->getObjectManager()->getRepository($this->entityName);

        $form = new GenericDoctrineDeleteForm($this->name, $this->options);
        $form->setHydrator(new DoctrineObject($this->objectManager));
        $form->setObject(new $this->entityName());
        $form->setInputFilter(
            $this->getInputFilterPluginManager()->get(
                GenericDoctrineDeleteFieldsetInputFilter::class,
                [
                    'object_manager' => $this->getObjectManager(),
                    'object_repository' => $entityRepository,
                    'translator' => $this->getTranslator(),
                ]
            )
        );
        $form->setObjectManager($this->getObjectManager());
        $form->setTranslator($this->getTranslator());
        $form->setOption('name_getter', $this->entityNameGetter);

        return $form;
    }
}