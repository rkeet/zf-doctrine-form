<?php

namespace Keet\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractDoctrineForm;

abstract class AbstractDoctrineFormFactory extends AbstractFormFactory
{
    /**
     * @var ObjectManager
     */
    protected $entityManager;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractDoctrineForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setEntityManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('translator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        $inputFilter = $this->getInputFilterPluginManager()->get($this->getFormInputFilter());

        $form = $this->getForm();

        /** @var AbstractDoctrineForm $form */
        $form = new $form($this->name, $this->options);
        $form->setObjectManager($this->getEntityManager());
        $form->setTranslator($this->getTranslator());
        $form->setInputFilter($inputFilter);

        return $form;
    }

    /**
     * Checks if received class is an Entity
     *
     * @param ObjectManager $objectManager
     * @param string|object $class
     *
     * @return boolean
     */
    function isEntity(ObjectManager $objectManager, $class)
    {
        if(is_object($class)){
            $class = ClassUtils::getClass($class);
        }

        return ! $objectManager->getMetadataFactory()->isTransient($class);
    }

    /**
     * @return ObjectManager
     */
    public function getEntityManager(): ObjectManager
    {
        return $this->entityManager;
    }

    /**
     * @param ObjectManager $entityManager
     * @return AbstractDoctrineFormFactory
     */
    public function setEntityManager(ObjectManager $entityManager): AbstractDoctrineFormFactory
    {
        $this->entityManager = $entityManager;
        return $this;
    }

}