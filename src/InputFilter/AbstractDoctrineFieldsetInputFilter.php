<?php

namespace Keet\Form\InputFilter;

use Doctrine\Common\Persistence\ObjectRepository;
use Zend\Di\Exception\InvalidArgumentException;
use Zend\Filter\ToInt;
use Zend\Filter\ToNull;
use Zend\I18n\Validator\IsInt;

abstract class AbstractDoctrineFieldsetInputFilter extends AbstractDoctrineInputFilter
{
    /**
     * @var ObjectRepository
     */
    protected $objectRepository;

    /**
     * AbstractDoctrineFieldsetInputFilter constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        // Check if ObjectRepository instance for InputFilter is set
        if ( ! isset($options['object_repository'])) {

            throw new InvalidArgumentException(
                'Required parameter "object_repository" not found. InputFilters require the Doctrine ObjectRepository.'
            );
        }

        if ( ! $options['object_repository'] instanceof ObjectRepository) {

            throw new InvalidArgumentException('Given ObjectRepository not an instance of ' . ObjectRepository::class);
        }
        $this->setObjectRepository($options['object_repository']);

        parent::__construct($options);
    }

    public function init()
    {
        $this->add(
            [
                'name'       => 'id',
                'required'   => true,
                'filters'    => [
                    ['name' => ToInt::class],
                    [
                        'name' => ToNull::class,
                        'options' => [
                            'type' => ToNull::TYPE_INTEGER,
                        ],
                    ],
                ],
                'validators' => [
                    ['name' => IsInt::class],
                ],
            ]
        );
    }

    /**
     * @return ObjectRepository
     */
    public function getObjectRepository() : ObjectRepository
    {
        return $this->objectRepository;
    }

    /**
     * @param ObjectRepository $objectRepository
     *
     * @return AbstractDoctrineFieldsetInputFilter
     */
    public function setObjectRepository(ObjectRepository $objectRepository) : AbstractDoctrineFieldsetInputFilter
    {
        $this->objectRepository = $objectRepository;
        return $this;
    }
}