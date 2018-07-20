<?php

namespace Keet\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Zend\InputFilter\InputFilterPluginManager;

/**
 * Class AbstractFieldsetInputFilterFactory
 *
 * @package Keet\Form\Factory
 *
 * Creating FieldsetInputFilterFactory classes is, alas, pretty much a custom job each and every time. However,
 * pretty much all of them have the same requirements when it comes to what they use. As such, we can provide
 * a class (this one) with some properties, getters/setters and a basic "apply the setters" function.
 */
abstract class AbstractDoctrineFieldsetInputFilterFactory extends AbstractFieldsetInputFilterFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * Use this function to setup the basic requirements commonly reused.
     *
     * @param ContainerInterface $container
     * @param string|null        $className
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setupRequirements(ContainerInterface $container, $className = null)
    {
        $this->setObjectManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterManager($container->get(InputFilterPluginManager::class));

        if (isset($className) && class_exists($className)) {
            $this->setObjectRepository(
                $this->getObjectManager()
                     ->getRepository($className)
            );
        }
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager() : ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     *
     * @return AbstractDoctrineFieldsetInputFilterFactory
     */
    public function setObjectManager(ObjectManager $objectManager) : AbstractDoctrineFieldsetInputFilterFactory
    {
        $this->objectManager = $objectManager;
        return $this;
    }

    /**
     * @return ObjectRepository
     */
    public function getObjectRepository() : ObjectRepository
    {
        return $this->objectRepository;
    }

    /**
     * @param ObjectRepository $objectRepository
     *
     * @return AbstractDoctrineFieldsetInputFilterFactory
     */
    public function setObjectRepository(ObjectRepository $objectRepository) : AbstractDoctrineFieldsetInputFilterFactory
    {
        $this->objectRepository = $objectRepository;
        return $this;
    }

}