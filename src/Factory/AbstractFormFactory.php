<?php

namespace Keet\Form\Factory;

use Exception;
use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractForm;
use ReflectionClass;
use ReflectionException;
use Zend\Form\FormElementManager\FormElementManagerV3Polyfill;
use Zend\Hydrator\HydratorInterface;
use Zend\InputFilter\InputFilterPluginManager;
use Zend\Mvc\I18n\Translator;
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
     * @var FormElementManagerV3Polyfill
     */
    protected $formElementManager;

    /**
     * @var string
     */
    protected $form;

    /**
     * @var string
     */
    protected $formInputFilter;

    /**
     * @var null|string
     */
    protected $entityName;

    /**
     * @var null|string
     */
    protected $hydrator;

    /**
     * AbstractFormFactory constructor.
     *
     * @param string      $form
     * @param string      $formInputFilter
     * @param string|null $hydrator
     * @param string|null $entityName
     *
     * @throws ReflectionException
     * @throws Exception
     */
    public function __construct(
        string $form,
        string $formInputFilter,
        string $hydrator = null,
        string $entityName = null
    ) {
        if ( ! class_exists($form)) {
            throw new Exception('AbstractFormFactory requires $form to be an existing form.', 500);
        }

        if ( ! class_exists($formInputFilter)) {
            throw new Exception(
                'AbstractFormFactory requires $formInputFilter to be an existing form InputFilter.',
                500
            );
        }

        $this->setForm($form);
        $this->setFormInputFilter($formInputFilter);

        if ($entityName && class_exists($entityName)) {
            $this->setEntityName($entityName);
        }

        if ($hydrator && class_exists($hydrator)
            && (new ReflectionClass($hydrator))->implementsInterface(HydratorInterface::class)
        ) {
            $this->setHydrator($hydrator);
        }
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return AbstractForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractForm
    {
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));
        $this->setFormElementManager($container->get('FormElementManager'));

        $inputFilter =
            $this->getInputFilterPluginManager()
                 ->get($this->getFormInputFilter());

        $form = $this->getForm();

        /** @var AbstractForm $form */
        $form = new $form($this->name, $this->options);
        $form->setTranslator($this->getTranslator());
        $form->setInputFilter($inputFilter);

        if ($this->getEntityName()) {
            $entity = $this->getEntityName();
            $form->setObject(new $entity());
        }

        if ($this->getHydrator()) {
            $hydrator = $this->getHydrator();
            $form->setHydrator(new $hydrator());
        }

        return $form;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * @param string $name
     *
     * @return AbstractFormFactory
     */
    public function setName(string $name) : AbstractFormFactory
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return array
     */
    public function getOptions() : array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return AbstractFormFactory
     */
    public function setOptions(array $options) : AbstractFormFactory
    {
        $this->options = $options;

        return $this;
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
     * @return AbstractFormFactory
     */
    public function setTranslator(Translator $translator) : AbstractFormFactory
    {
        $this->translator = $translator;

        return $this;
    }

    /**
     * @return InputFilterPluginManager
     */
    public function getInputFilterPluginManager() : InputFilterPluginManager
    {
        return $this->inputFilterPluginManager;
    }

    /**
     * @param InputFilterPluginManager $inputFilterPluginManager
     *
     * @return AbstractFormFactory
     */
    public function setInputFilterPluginManager(
        InputFilterPluginManager $inputFilterPluginManager
    ) : AbstractFormFactory {
        $this->inputFilterPluginManager = $inputFilterPluginManager;

        return $this;
    }

    /**
     * @return string
     */
    public function getForm() : string
    {
        return $this->form;
    }

    /**
     * @param string $form
     *
     * @return AbstractFormFactory
     */
    public function setForm(string $form) : AbstractFormFactory
    {
        $this->form = $form;

        return $this;
    }

    /**
     * @return string
     */
    public function getFormInputFilter() : string
    {
        return $this->formInputFilter;
    }

    /**
     * @param string $formInputFilter
     *
     * @return AbstractFormFactory
     */
    public function setFormInputFilter(string $formInputFilter) : AbstractFormFactory
    {
        $this->formInputFilter = $formInputFilter;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getEntityName() : ?string
    {
        return $this->entityName;
    }

    /**
     * @param string|null $entityName
     *
     * @return AbstractFormFactory
     */
    public function setEntityName(?string $entityName) : AbstractFormFactory
    {
        $this->entityName = $entityName;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getHydrator() : ?string
    {
        return $this->hydrator;
    }

    /**
     * @param string|null $hydrator
     *
     * @return AbstractFormFactory
     */
    public function setHydrator(?string $hydrator) : AbstractFormFactory
    {
        $this->hydrator = $hydrator;

        return $this;
    }

    /**
     * @return FormElementManagerV3Polyfill
     */
    public function getFormElementManager() : FormElementManagerV3Polyfill
    {
        return $this->formElementManager;
    }

    /**
     * @param FormElementManagerV3Polyfill $formElementManager
     *
     * @return AbstractFormFactory
     */
    public function setFormElementManager(FormElementManagerV3Polyfill $formElementManager) : AbstractFormFactory
    {
        $this->formElementManager = $formElementManager;

        return $this;
    }

}