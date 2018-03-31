<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\I18n\Translator\Translator;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

/**
 * Class AbstractFieldsetInputFilterFactory
 * @package Keet\Form\Factory
 *
 * Creating FieldsetInputFilterFactory classes is, alas, pretty much a custom job each and every time. However,
 * pretty much all of them have the same requirements when it comes to what they use. As such, we can provide
 * a class (this one) with some properties, getters/setters and a basic "apply the setters" function.
 */
abstract class AbstractFieldsetInputFilterFactory implements FactoryInterface
{
    /**
     * @var InputFilterPluginManager
     */
    protected $inputFilterManager;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * Use this function to setup the basic requirements commonly reused.
     *
     * @param ContainerInterface $container
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function setupRequirements(ContainerInterface $container)
    {
        $this->setTranslator($container->get('translator'));
        $this->setInputFilterManager($container->get(InputFilterPluginManager::class));
    }

    /**
     * @return InputFilterPluginManager
     */
    public function getInputFilterManager(): InputFilterPluginManager
    {
        return $this->inputFilterManager;
    }

    /**
     * @param InputFilterPluginManager $inputFilterManager
     * @return AbstractFieldsetInputFilterFactory
     */
    public function setInputFilterManager(InputFilterPluginManager $inputFilterManager): AbstractFieldsetInputFilterFactory
    {
        $this->inputFilterManager = $inputFilterManager;
        return $this;
    }

    /**
     * @return Translator
     */
    public function getTranslator(): Translator
    {
        return $this->translator;
    }

    /**
     * @param Translator $translator
     * @return AbstractFieldsetInputFilterFactory
     */
    public function setTranslator(Translator $translator): AbstractFieldsetInputFilterFactory
    {
        $this->translator = $translator;
        return $this;
    }

}