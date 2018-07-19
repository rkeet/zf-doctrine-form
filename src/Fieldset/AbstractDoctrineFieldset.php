<?php

namespace Keet\Form\Fieldset;

use Doctrine\Common\Persistence\ObjectManager;

abstract class AbstractDoctrineFieldset extends AbstractFieldset
{
    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * AbstractFieldset constructor.
     *
     * @param ObjectManager $objectManager
     * @param string        $name Lower case short class name
     * @param array         $options
     */
    public function __construct(ObjectManager $objectManager, string $name, array $options = [])
    {
        parent::__construct($name, $options);

        $this->setObjectManager($objectManager);
    }

    /**
     * @return ObjectManager
     */
    public function getObjectManager() : ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     *
     * @return AbstractDoctrineFieldset
     */
    public function setObjectManager(ObjectManager $objectManager) : AbstractDoctrineFieldset
    {
        $this->objectManager = $objectManager;
        return $this;
    }

}