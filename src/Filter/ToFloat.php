<?php

namespace Keet\Form\Filter;

use Zend\Filter\AbstractFilter;

class ToFloat extends AbstractFilter
{
    /**
     * @param mixed $value
     * @return float
     */
    public function filter($value)
    {
        $float = $value;

        if ($value) {
            $float = str_replace(',', '.', $value);
        }

        return (float) $float;
    }
}