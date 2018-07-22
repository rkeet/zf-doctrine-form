<?php

namespace Keet\Form\Form;

use Zend\Form\Element\Hidden;
use Zend\Form\Element\Select;
use Zend\Form\Element\Submit;

/**
 * Class GenericDeleteForm
 *
 * @package Keet\Form\Form
 *
 * @deprecated 2018-07-22 RK: I work mainly with Doctrine, keeping this updated keeps coming back as an afterthought.
 *             Will remove in future release. Doctrine version will remain.
 */
class GenericDeleteForm extends AbstractForm
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->add(
            [
                'name' => 'id',
                'type' => Hidden::class,
            ]
        );

        $this->add(
            [
                'name'       => 'delete',
                'required'   => true,
                'type'       => Select::class,
                'options'    => [
                    'label'         => _('Confirm deletion'),
                    'value_options' => [
                        'yes' => _('Yes, delete it.'),
                        'no'  => _('I made a mistake, take me back.'),
                    ],
                ],
                'attributes' => [
                    'value' => 'no',
                ],
            ]
        );

        $this->add(
            [
                'name'       => 'submit',
                'type'       => Submit::class,
                'attributes' => [
                    'value' => _('Confirm action'),
                ],
            ]
        );

        //Call parent initializer. Check in parent what it does.
        parent::init();
    }
}