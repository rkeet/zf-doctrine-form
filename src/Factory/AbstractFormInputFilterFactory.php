<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\Mvc\I18n\Translator;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

abstract class AbstractFormInputFilterFactory implements FactoryInterface
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var InputFilterPluginManager
     */
    protected $inputFilterManager;

    /**
     * Use this function to setup the basic requirements commonly reused.
     *
     * @param ContainerInterface $container
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setupRequirements(ContainerInterface $container)
    {
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterManager($container->get(InputFilterPluginManager::class));
    }

    /**
     * @return Translator
     */
    public function getTranslator() : Translator
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     *
     * @return AbstractFormInputFilterFactory
     */
    public function setTranslator(Translator $translator) : AbstractFormInputFilterFactory
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return InputFilterPluginManager
     */
    public function getInputFilterManager() : InputFilterPluginManager
    {
        return $this->inputFilterManager;
    }

    /**
     * @param InputFilterPluginManager $inputFilterManager
     *
     * @return AbstractFormInputFilterFactory
     */
    public function setInputFilterManager(InputFilterPluginManager $inputFilterManager
    ) : AbstractFormInputFilterFactory {
        $this->inputFilterManager = $inputFilterManager;
        return $this;
    }

}