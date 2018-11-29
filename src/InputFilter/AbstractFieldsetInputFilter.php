<?php

namespace Keet\Form\InputFilter;

use Zend\Filter\ToInt;
use Zend\I18n\Validator\IsInt;

abstract class AbstractFieldsetInputFilter extends AbstractInputFilter
{
    /**
     * {@inheritdoc}
     */
    public function init()
    {
        $this->add(
            [
                'name'       => 'id',
                'required'   => false,
                'filters'    => [
                    ['name' => ToInt::class],
                ],
                'validators' => [
                    ['name' => IsInt::class],
                ],
            ]
        );
    }
}