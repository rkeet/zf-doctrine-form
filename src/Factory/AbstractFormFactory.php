<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractForm;
use Zend\Di\Exception\ClassNotFoundException;
use Zend\I18n\Translator\Translator;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\ServiceManager\Factory\FactoryInterface;

abstract class AbstractFormFactory implements FactoryInterface
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $options;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var InputFilterPluginManager
     */
    protected $inputFilterPluginManager;

    /**
     * @var string
     */
    protected $form;

    /**
     * @var string
     */
    protected $formInputFilter;

    /**
     * AbstractFormFactory constructor.
     * @param $form
     * @param $formInputFilter
     */
    public function __construct($form, $formInputFilter)
    {
        if (!class_exists($form)) {

            throw new ClassNotFoundException('AbstractFormFactory requires $form to be an existing form.');
        }

        if (!class_exists($formInputFilter)) {

            throw new ClassNotFoundException('AbstractFormFactory requires $formInputFilter to be an existing form InputFilter.');
        }

        $this->setForm($form);
        $this->setFormInputFilter($formInputFilter);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setTranslator($container->get('translator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        $inputFilter = $this->getInputFilterPluginManager()->get($this->getFormInputFilter());

        $form = $this->getForm();

        /** @var AbstractForm $form */
        $form = new $form($this->name, $this->options);
        $form->setTranslator($this->getTranslator());
        $form->setInputFilter($inputFilter);

        return $form;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return AbstractFormFactory
     */
    public function setName(string $name): AbstractFormFactory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     * @return AbstractFormFactory
     */
    public function setOptions(array $options): AbstractFormFactory
    {
        $this->options = $options;
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
     * @return AbstractFormFactory
     */
    public function setTranslator(Translator $translator): AbstractFormFactory
    {
        $this->translator = $translator;
        return $this;
    }

    /**
     * @return InputFilterPluginManager
     */
    public function getInputFilterPluginManager(): InputFilterPluginManager
    {
        return $this->inputFilterPluginManager;
    }

    /**
     * @param InputFilterPluginManager $inputFilterPluginManager
     * @return AbstractFormFactory
     */
    public function setInputFilterPluginManager(InputFilterPluginManager $inputFilterPluginManager): AbstractFormFactory
    {
        $this->inputFilterPluginManager = $inputFilterPluginManager;
        return $this;
    }

    /**
     * @return string
     */
    public function getForm(): string
    {
        return $this->form;
    }

    /**
     * @param string $form
     * @return AbstractFormFactory
     */
    public function setForm(string $form): AbstractFormFactory
    {
        $this->form = $form;
        return $this;
    }

    /**
     * @return string
     */
    public function getFormInputFilter(): string
    {
        return $this->formInputFilter;
    }

    /**
     * @param string $formInputFilter
     * @return AbstractFormFactory
     */
    public function setFormInputFilter(string $formInputFilter): AbstractFormFactory
    {
        $this->formInputFilter = $formInputFilter;
        return $this;
    }

}