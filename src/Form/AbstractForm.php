<?php

namespace Keet\Form\Form;

use Zend\Form\Element\Csrf;
use Zend\Form\Element\Submit;
use Zend\Form\ElementInterface;
use Zend\Form\Exception\InvalidElementException;
use Zend\Form\Form;
use Zend\I18n\Translator\TranslatorAwareTrait;
use Zend\InputFilter\InputFilterAwareInterface;

abstract class AbstractForm extends Form implements InputFilterAwareInterface
{
    use TranslatorAwareTrait;

    /**
     * CSRF timeout in seconds
     */
    protected $csrfTimeout = 900; // 15 minutes

    /**
     * {@inheritDoc}
     */
    public function __construct($name = null, $options = [])
    {
        $csrfName = null;
        if (isset($options['csrfCorrector'])) {
            $csrfName = $options['csrfCorrector'];
            unset($options['csrfCorrector']);
        }

        parent::__construct($name, $options);

        if ($csrfName === null) {
            $csrfName = 'csrf';
        }

        $this->addElementCsrf($csrfName);
    }

    /**
     * NOTE: OVERRIDING ORIGINAL
     *
     * Needs to be overridden. Original always creates array ($data[$name]) values, this must not always happen,
     * for example when a fieldset is empty.
     *
     * {@inheritdoc}
     */
    protected function prepareBindData(array $values, array $match)
    {
        $data = [];
        foreach ($values as $name => $value) {
            if ( ! array_key_exists($name, $match)) {
                continue;
            }

            if (is_array($value) && is_array($match[$name])) {
                if ( ! empty(array_filter($value))) {
                    $data[$name] = $this->prepareBindData($value, $match[$name]);
                }
            } else {
                $data[$name] = $value;
            }
        }
        return $data;
    }

    /**
     * This function is automatically called when creating element with factory. It
     * allows to perform various operations (add elements...)
     *
     * @return void
     */
    public function init()
    {
        if ( ! $this->has('submit')) {
            $this->addSubmitButton();
        }
    }

    /**
     * Used to add a submit button to the Form.
     *
     * Overwrite default usage of this function by adding your own element with 'name' => 'submit' to your Form.
     *
     * @param string     $value
     * @param array|null $classes
     */
    public function addSubmitButton($value = 'Save', array $classes = null)
    {
        $this->add(
            [
                'name'       => 'submit',
                'type'       => Submit::class,
                'attributes' => [
                    'value' => $value,
                    'class' => (! is_null($classes) ? join(' ', $classes) : 'btn btn-primary'),
                ],
            ]
        );
    }

    /**
     * Retrieve a named element or fieldset
     *
     * Extends Zend\Form with CSRF fields that can be retrieved by the name "CSRF"
     * but are resolved to their unique name
     *
     * @param  string $elementOrFieldset
     *
     * @throws InvalidElementException
     * @return ElementInterface
     */
    public function get($elementOrFieldset)
    {
        if ($elementOrFieldset === 'csrf') {
            // Find CSRF element
            foreach ($this->elements as $formElement) {
                if ($formElement instanceof Csrf) {
                    return $formElement;
                }
            }
        }

        return parent::get($elementOrFieldset);
    }

    /**
     * Adds CSRF protection
     */
    protected function addElementCsrf($csrfName = 'csrf')
    {
        $this->add(
            [
                'type'    => Csrf::class,
                'name'    => $csrfName,
                'options' => [
                    'csrf_options' => [
                        'timeout' => $this->csrfTimeout,
                    ],
                ],
            ]
        );
    }
}