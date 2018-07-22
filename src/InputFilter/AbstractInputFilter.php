<?php

namespace Keet\Form\InputFilter;

use Zend\Di\Exception\InvalidArgumentException;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\InputFilter\InputFilter;
use Zend\Mvc\I18n\Translator;

/**
 * Class adds $required property, making it available on an InputFilter.
 *
 * When constructing an InputFilter for a Fieldset or Collection, you can now mark these to be required, or not.
 *
 * The isValid function of the parent is overridden in this class. The function does a pre-check to see if the the
 * current object to check is required. If not a further check is done to see if the element is empty. This check is
 * done using the arrayFilterEmpty function. This function recursively loops objects to see if they're empty. As such,
 * a structure could have nested non-required empty objects, which will be skipped from validation. If any child
 * contains a bit of data, the parent will be checked as per normal rules of Zend Framework.
 */
abstract class AbstractInputFilter extends InputFilter
{
    use TranslatorAwareTrait;

    /**
     * Can be used for setting a Fieldset to not be required.
     *
     * If 'something' (ie Fieldset) is not required, the `isValid()` method will validate an empty
     * fieldset as valid. See below.
     *
     * @var bool
     */
    private $required = true;

    /**
     * AbstractFormInputFilter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Check for presence of translator so as to translate return messages
        if ( ! isset($options['translator'])) {

            throw new InvalidArgumentException(
                'Required parameter "translator" not found. InputFilters require the Zend\Mvc\I18n\Translator.'
            );
        }

        if ( ! $options['translator'] instanceof Translator) {

            throw new InvalidArgumentException(
                'Incorrect translator was given for the InputFilter. Required to be instance of "Zend\Mvc\I18n\Translator".'
            );
        }
        $this->setTranslator($options['translator']);
    }

    /**
     * @param array $array
     *
     * @return bool
     */
    function arrayFilterEmpty($array)
    {
        $rest = array_filter($array);
        if (empty($rest)) {

            return true;
        }

        foreach ($rest as $key => $value) {
            if ( ! is_array($value) && ! empty($value)) {
                return false;
            }

            if (is_array($value)) {
                if (empty(array_filter($value))) {
                    $rest[$key] = true;
                } else {
                    $rest[$key] = $this->arrayFilterEmpty($value);
                }
            } else {
                $rest[$key] = false;
            }
        }

        return ! in_array(false, $rest);
    }

    /**
     * {@inheritdoc}
     */
    public function isValid($context = null)
    {
        if ( ! $this->isRequired() && $this->arrayFilterEmpty($this->getRawValues())) {

            return true;
        }

        return parent::isValid();
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $required
     *
     * @return $this
     */
    public function setRequired($required)
    {
        $this->required = (bool) $required;
        return $this;
    }
}