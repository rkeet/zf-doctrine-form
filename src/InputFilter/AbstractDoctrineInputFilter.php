<?php

namespace Keet\Form\InputFilter;

use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractDoctrineInputFilter extends AbstractInputFilter
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * AbstractFormInputFilter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Check if ObjectManager|EntityManager for FormInputFilter is set
        if (isset($options['object_manager']) && $options['object_manager'] instanceof ObjectManager) {

            $this->setObjectManager($options['object_manager']);
        }

        parent::__construct($options);
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     * @return AbstractDoctrineInputFilter
     */
    public function setObjectManager(ObjectManager $objectManager): AbstractDoctrineInputFilter
    {
        $this->objectManager = $objectManager;
        return $this;
    }

}