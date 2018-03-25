<?php

namespace Keet\Form\InputFilter;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Persistence\ObjectRepository;
use Zend\Di\Exception\InvalidArgumentException;

abstract class AbstractDoctrineFieldsetInputFilter extends AbstractDoctrineInputFilter
{
    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * AbstractDoctrineFieldsetInputFilter constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Check if ObjectManager|EntityManager for InputFilter is set
        if (!isset($options['object_manager'])) {

            throw new InvalidArgumentException('Required parameter "object_manager" not found. InputFilters require the Doctrine ObjectManager|EntityManager.');
        }

        if (!$options['object_manager'] instanceof ObjectManager) {

            throw new InvalidArgumentException('Given ObjectManager not an instance of Doctrines\' ObjectManager|EntityManager.');
        }
        $this->setObjectManager($options['object_manager']);

        // Check if ObjectRepository instance for InputFilter is set
        if (!isset($options['object_repository'])) {

            throw new InvalidArgumentException('Required parameter "object_repository" not found. InputFilters require the Doctrine ObjectRepository.');
        }

        if (!$options['object_repository'] instanceof ObjectRepository) {

            throw new InvalidArgumentException('Given ObjectRepository not an instance of ' . ObjectRepository::class);
        }
        $this->setObjectRepository($options['object_repository']);

        parent::__construct($options);
    }

    /**
     * @return ObjectRepository
     */
    public function getObjectRepository(): ObjectRepository
    {
        return $this->objectRepository;
    }

    /**
     * @param ObjectRepository $objectRepository
     * @return AbstractDoctrineFieldsetInputFilter
     */
    public function setObjectRepository(ObjectRepository $objectRepository): AbstractDoctrineFieldsetInputFilter
    {
        $this->objectRepository = $objectRepository;
        return $this;
    }
}