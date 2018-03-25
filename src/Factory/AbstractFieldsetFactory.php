<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Keet\Form\Fieldset\AbstractFieldset;
use Zend\Di\Exception\ClassNotFoundException;
use Zend\Hydrator\Reflection;
use Zend\I18n\Translator\Translator;
use Zend\ServiceManager\Factory\FactoryInterface;

abstract class AbstractFieldsetFactory implements FactoryInterface
{
    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $fieldset;

    /**
     * @var string
     */
    protected $fieldsetName;

    /**
     * @var string
     */
    protected $fieldsetObject;

    /**
     * AbstractFieldsetFactory constructor.
     * @param $fieldset
     * @param $name
     * @param $fieldsetObject
     * @throws ClassNotFoundException
     */
    public function __construct($fieldset, $name, $fieldsetObject)
    {
        if (!class_exists($fieldset)) {

            throw new ClassNotFoundException('AbstractFieldsetFactory parameter $fieldset should be the name of an existing Fieldset.');
        }

        if (!class_exists($fieldsetObject)){

            throw new ClassNotFoundException('AbstractFieldsetFactory parameter $fieldsetObject should be the name of an existing Entity.');
        }

        $this->setFieldset($fieldset);
        $this->setFieldsetName($name);
        $this->setFieldsetObject($fieldsetObject);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return AbstractFieldset
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setTranslator($container->get('translator'));

        $fieldset = $this->getFieldset();
        $fieldsetObject = $this->getFieldsetObject();

        /** @var AbstractFieldset $fieldset */
        $fieldset = new $fieldset($this->name ?: $this->getFieldsetName());
        $fieldset->setHydrator(new Reflection());
        $fieldset->setObject(new $fieldsetObject());
        $fieldset->setTranslator($this->getTranslator());

        return $fieldset;
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
     * @return AbstractFieldsetFactory
     */
    public function setTranslator(Translator $translator): AbstractFieldsetFactory
    {
        $this->translator = $translator;
        return $this;
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
     * @return AbstractFieldsetFactory
     */
    public function setName(string $name): AbstractFieldsetFactory
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldset(): string
    {
        return $this->fieldset;
    }

    /**
     * @param string $fieldset
     * @return AbstractFieldsetFactory
     */
    public function setFieldset(string $fieldset): AbstractFieldsetFactory
    {
        $this->fieldset = $fieldset;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldsetName(): string
    {
        return $this->fieldsetName;
    }

    /**
     * @param string $fieldsetName
     * @return AbstractFieldsetFactory
     */
    public function setFieldsetName(string $fieldsetName): AbstractFieldsetFactory
    {
        $this->fieldsetName = $fieldsetName;
        return $this;
    }

    /**
     * @return string
     */
    public function getFieldsetObject(): string
    {
        return $this->fieldsetObject;
    }

    /**
     * @param string $fieldsetObject
     * @return AbstractFieldsetFactory
     */
    public function setFieldsetObject(string $fieldsetObject): AbstractFieldsetFactory
    {
        $this->fieldsetObject = $fieldsetObject;
        return $this;
    }

}