<?php

namespace Keet\Form\InputFilter;

use Zend\Validator\InArray;

/**
 * Class GenericDeleteFieldsetInputFilter
 *
 * @package Keet\Form\InputFilter
 *
 * @deprecated 2018-07-22 RK: I work mainly with Doctrine, keeping this updated keeps coming back as an afterthought.
 *             Will remove in future release. Doctrine version will remain.
 */
class GenericDeleteFieldsetInputFilter extends AbstractFieldsetInputFilter
{
    public function init()
    {
        parent::init();

        $this->add(
            [
                'name'       => 'delete',
                'required'   => true,
                'filters'    => [],
                'validators' => [
                    [
                        'name'    => InArray::class,
                        'options' => [
                            'haystack' => [
                                'yes',
                                'no',
                            ],
                        ],
                        'strict'  => InArray::COMPARE_NOT_STRICT,
                    ],
                ],
            ]
        );
    }
}