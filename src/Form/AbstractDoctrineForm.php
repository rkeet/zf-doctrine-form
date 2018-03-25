<?php

namespace Keet\Form\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Persistence\ObjectManagerAwareInterface;

abstract class AbstractDoctrineForm extends AbstractForm implements ObjectManagerAwareInterface
{
    /**
     * No need to set this in Factory. However, if your form is going to use Entities (e.g. for ObjectSelect), then
     * make sure to set this!
     *
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @return ObjectManager
     */
    public function getObjectManager(): ObjectManager
    {
        return $this->objectManager;
    }

    /**
     * @param ObjectManager $objectManager
     * @return AbstractDoctrineForm
     */
    public function setObjectManager(ObjectManager $objectManager): AbstractDoctrineForm
    {
        $this->objectManager = $objectManager;
        return $this;
    }

}