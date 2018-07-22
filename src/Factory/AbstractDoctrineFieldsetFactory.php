<?php

namespace Keet\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Keet\Form\Fieldset\AbstractDoctrineFieldset;

abstract class AbstractDoctrineFieldsetFactory extends AbstractFieldsetFactory
{
    /**
     * @var ObjectManager|EntityManager
     */
    protected $objectManager;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AbstractDoctrineFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setObjectManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('MvcTranslator'));

        $fieldset = $this->getFieldset();
        $fieldsetObject = $this->getFieldsetObject();

        /** @var AbstractDoctrineFieldset $fieldset */
        $fieldset = new $fieldset($this->getObjectManager(), $this->name ?: $this->getFieldsetName());
        $fieldset->setHydrator(
            new DoctrineObject($this->getObjectManager())
        );
        $fieldset->setObject(new $fieldsetObject());
        $fieldset->setTranslator($this->getTranslator());

        return $fieldset;
    }

    /**
     * @return EntityManager
     */
    public function getObjectManager() : ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     *
     * @return AbstractDoctrineFieldsetFactory
     */
    public function setObjectManager(ObjectManager $objectManager) : AbstractDoctrineFieldsetFactory
    {
        $this->objectManager = $objectManager;
        return $this;
    }

}