<?php

namespace Keet\Form\InputFilter;

use Zend\Validator\Csrf;

abstract class AbstractFormInputFilter extends AbstractInputFilter
{
    /**
     * Init function
     */
    public function init()
    {
        // If CSRF validation has not been added, add it here
        if ( ! $this->has('csrf')) {
            $this->add(
                [
                    'name'       => 'csrf',
                    'required'   => true,
                    'filters'    => [],
                    'validators' => [
                        [
                            'name'    => Csrf::class,
                            'options' => [
                                'messages' => [
                                    Csrf::NOT_SAME => _(
                                        'The form expired or could otherwise not be validated. Please try again.'
                                    ),
                                ],
                            ],
                        ],
                    ],
                ]
            );
        }
    }
}