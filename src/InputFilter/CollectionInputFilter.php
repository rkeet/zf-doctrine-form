<?php

namespace Keet\Form\InputFilter;

use InvalidArgumentException;
use Traversable;

class CollectionInputFilter extends \Zend\InputFilter\CollectionInputFilter
{
    /**
     * NOTE: Overriding original. This implementation only returns a negative result and only sets
     * messages when the object being validated is considered invalid. Original implementation always sets
     * return messages, as such is not a reliable method for determining whether a Collection is valid and/or
     * whether only child Fieldsets, which have data, have been checked (as empty non-required child-Fieldsets
     * need not cause an invalid return).
     *
     * {@inheritdoc}
     */
    public function isValid($context = null)
    {
        $inputFilter = $this->getInputFilter();
        $valid = true;

        if ($this->getCount() < 1) {
            if ($this->isRequired) {
                $valid = false;
            }
        }

        if (count($this->data) < $this->getCount()) {
            $valid = false;
        }

        if ( ! $this->data) {
            $this->clearValues();
            $this->clearRawValues();

            return $valid;
        }

        foreach ($this->data as $key => $data) {
            $inputFilter->setData($data);

            if (null !== $this->validationGroup) {
                $inputFilter->setValidationGroup($this->validationGroup[$key]);
            }

            if ($inputFilter->isValid()) {
                if (empty($inputFilter->getValidInput())) {

                    return $valid;
                }

                $this->validInputs[$key] = $inputFilter->getValidInput();
            } else {
                $valid = false;
                $this->collectionMessages[$key] = $inputFilter->getMessages();
                $this->invalidInputs[$key] = $inputFilter->getInvalidInput();
            }

            $this->collectionValues[$key] = $inputFilter->getValues();

            // https://github.com/zendframework/zend-inputfilter/pull/169
            //            $this->collectionRawValues[$key] = $inputFilter->getRawValues();
        }

        return $valid;
    }

    /**
     * {@inheritdoc}
     *
     * Sets the collectionRawValues without any modifications.
     */
    public function setData($data)
    {
        if ( ! (is_array($data) || $data instanceof Traversable)) {
            throw new InvalidArgumentException(
                sprintf(
                    '%s expects an array or Traversable collection; invalid collection of type %s provided',
                    __METHOD__,
                    is_object($data) ? get_class($data) : gettype($data)
                )
            );
        }

        // https://github.com/zendframework/zend-inputfilter/pull/169
        $this->collectionRawValues = $data;

        foreach ($data as $item) {
            if (is_array($item) || $item instanceof Traversable) {
                continue;
            }

            throw new InvalidArgumentException(
                sprintf(
                    '%s expects each item in a collection to be an array or Traversable; '
                    . 'invalid item in collection of type %s detected',
                    __METHOD__,
                    is_object($item) ? get_class($item) : gettype($item)
                )
            );
        }

        $this->data = $data;
        return $this;
    }
}
