<?php

namespace Keet\Form\Fieldset;

use Zend\Form\Fieldset;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\InputFilter\InputFilterAwareTrait;

abstract class AbstractFieldset extends Fieldset
{
    use InputFilterAwareTrait;
    use TranslatorAwareTrait;
}