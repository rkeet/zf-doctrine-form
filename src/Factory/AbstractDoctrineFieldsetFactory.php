<?php

namespace Keet\Form\Factory;

use Doctrine\ORM\EntityManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Interop\Container\ContainerInterface;
use Keet\Form\Fieldset\AbstractDoctrineFieldset;

abstract class AbstractDoctrineFieldsetFactory extends AbstractFieldsetFactory
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractDoctrineFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setEntityManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('translator'));

        $fieldset = $this->getFieldset();
        $fieldsetObject = $this->getFieldsetObject();

        /** @var AbstractDoctrineFieldset $fieldset */
        $fieldset = new $fieldset($this->getEntityManager(), $this->name ?: $this->getFieldsetName());
        $fieldset->setHydrator(
            new DoctrineObject($this->getEntityManager())
        );
        $fieldset->setObject(new $fieldsetObject());
        $fieldset->setTranslator($this->getTranslator());

        return $fieldset;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager(): EntityManager
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     * @return AbstractDoctrineFieldsetFactory
     */
    public function setEntityManager(EntityManager $entityManager): AbstractDoctrineFieldsetFactory
    {
        $this->entityManager = $entityManager;
        return $this;
    }

}