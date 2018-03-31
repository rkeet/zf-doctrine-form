<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Zend\Hydrator\Reflection;

class GenericDeleteFormFactory extends AbstractFormFactory
{
    public function __construct()
    {
        parent::__construct(GenericDeleteForm::class, GenericDeleteFieldsetInputFilter::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string $requestedName
     * @param array|null $options
     * @return GenericDeleteForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    {
        $this->setTranslator($container->get('translator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        $form = new GenericDeleteForm($this->name, $this->options);
        $form->setHydrator(new Reflection());
        $form->setInputFilter(
            $this->getInputFilterPluginManager()->get(
                GenericDeleteFieldsetInputFilter::class,
                [
                    'object_manager' => $this->getObjectManager(),
                    'object_repository' => $entityRepository,
                    'translator' => $this->getTranslator(),
                ]
            )
        );
        $form->setTranslator($this->getTranslator());
        $form->setOption('name_getter', $this->entityNameGetter);

        return $form;
    }
}