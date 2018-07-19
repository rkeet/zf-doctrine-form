<?php

namespace Keet\Form\InputFilter;

use Zend\Filter\ToInt;
use Zend\Filter\ToNull;
use Zend\I18n\Validator\IsInt;

abstract class AbstractFieldsetInputFilter extends AbstractInputFilter
{
    /**
     * Init function
     */
    public function init()
    {
        $this->add(
            [
                'name'       => 'id',
                'required'   => false,
                'filters'    => [
                    ['name' => ToInt::class],
                    [
                        'name' => ToNull::class,
                        'options' => [
                            'type' => ToNull::TYPE_INTEGER,
                        ],
                    ],
                ],
                'validators' => [
                    ['name' => IsInt::class],
                ],
            ]
        );
    }
}