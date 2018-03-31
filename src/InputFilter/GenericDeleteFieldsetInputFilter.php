<?php

namespace Keet\Form\InputFilter;

use Zend\Validator\InArray;

class GenericDeleteFieldsetInputFilter extends AbstractFieldsetInputFilter
{
    public function init()
    {
        parent::init();

        $this->add([
            'name' => 'delete',
            'required' => true,
            'filters' => [],
            'validators' => [
                [
                    'name' => InArray::class,
                    'options' => [
                        'haystack' => [
                            'yes',
                            'no',
                        ],
                    ],
                    'strict' => InArray::COMPARE_NOT_STRICT,
                ],
            ],
        ]);
    }
}