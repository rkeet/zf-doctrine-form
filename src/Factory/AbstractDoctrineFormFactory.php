<?php

namespace Keet\Form\Factory;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Util\ClassUtils;
use Doctrine\ORM\EntityManager;
use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractDoctrineForm;
use Keet\Form\Form\AbstractForm;

abstract class AbstractDoctrineFormFactory extends AbstractFormFactory
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AbstractDoctrineForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractForm
    {
        $this->setObjectManager($container->get(EntityManager::class));
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        $inputFilter = $this->getInputFilterPluginManager()
                            ->get($this->getFormInputFilter());

        $form = $this->getForm();

        /** @var AbstractDoctrineForm $form */
        $form = new $form($this->name, $this->options);
        $form->setObjectManager($this->getObjectManager());
        $form->setTranslator($this->getTranslator());
        $form->setInputFilter($inputFilter);

        return $form;
    }

    /**
     * Checks if received class is an Entity
     *
     * @param ObjectManager $objectManager
     * @param string|\object $class
     *
     * @return boolean
     */
    function isEntity(ObjectManager $objectManager, $class)
    {
        if (is_object($class)) {
            $class = ClassUtils::getClass($class);
        }

        return ! $objectManager->getMetadataFactory()
                               ->isTransient($class);
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
     * @return AbstractDoctrineFormFactory
     */
    public function setObjectManager(ObjectManager $objectManager) : AbstractDoctrineFormFactory
    {
        $this->objectManager = $objectManager;
        return $this;
    }

}