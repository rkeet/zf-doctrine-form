<?php

namespace Keet\Form\InputFilter;

use Zend\Validator\Csrf;

/**
 * NOTE: at time of writing this class is an exact duplicate of AbstractFormInputFilter.
 * This class is created purely to keep the structures the same and be here in case something gets required in the
 * future requiring something of Doctrine to be injected into the FormInputFilter class
 */
abstract class AbstractDoctrineFormInputFilter extends AbstractDoctrineInputFilter
{
    /**
     * Init function
     */
    public function init()
    {
        // If CSRF validation has not been added, add it here
        if (!$this->has('csrf')) {
            $this->add([
                'name' => 'csrf',
                'required' => true,
                'filters' => [],
                'validators' => [
                    ['name' => Csrf::class],
                ],
            ]);
        }
    }
}