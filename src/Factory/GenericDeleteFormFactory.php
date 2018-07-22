<?php

namespace Keet\Form\Factory;

use Interop\Container\ContainerInterface;
use Keet\Form\Form\AbstractForm;
use Keet\Form\Form\GenericDeleteForm;
use Keet\Form\InputFilter\GenericDeleteFieldsetInputFilter;
use Zend\Hydrator\Reflection;

/**
 * Class GenericDeleteFormFactory
 *
 * @package    Keet\Form\Factory
 *
 * @deprecated 2018-07-22 RK: I work mainly with Doctrine, keeping this updated keeps coming back as an afterthought.
 *             Will remove in future release. Doctrine version will remain.
 */
class GenericDeleteFormFactory extends AbstractFormFactory
{
    public function __construct()
    {
        parent::__construct(GenericDeleteForm::class, GenericDeleteFieldsetInputFilter::class);
    }

    /**
     * @param ContainerInterface $container
     * @param string             $requestedName
     * @param array|null         $options
     *
     * @return GenericDeleteForm
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null) : AbstractForm
    {
        $this->setTranslator($container->get('MvcTranslator'));
        $this->setInputFilterPluginManager($container->get('InputFilterManager'));

        $form = new GenericDeleteForm($this->name, $this->options);
        $form->setHydrator(new Reflection());
        $form->setInputFilter(
            $this->getInputFilterPluginManager()
                 ->get(
                     GenericDeleteFieldsetInputFilter::class,
                     [
                         'translator' => $this->getTranslator(),
                     ]
                 )
        );
        $form->setTranslator($this->getTranslator());
        $form->setOption('name_getter', $options['name_getter']);

        return $form;
    }
}