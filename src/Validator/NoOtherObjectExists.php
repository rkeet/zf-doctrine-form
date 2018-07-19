<?php

namespace Keet\Form\Validator;

use DoctrineModule\Validator\NoObjectExists;

/**
 * Class NoOtherObjectExists
 *
 * @package Keet\Form\Validator
 *
 * =======================
 *
 * TIP: Best set this requirement in Factory (which loads the form). ID for comparison object (the one you're
 * modifying) is required.
 *
 * In Factory that sets this Validator on Input:
 * @var \Zend\Router\Http\TreeRouteStack $router
 *     $router = $this->getServiceManager()->get('router');
 * @var \Zend\Http\Request               $request
 *     $request = $this->getServiceManager()->get('request');
 *
 * Then get the required ID: $router->match($request)->getParam('id')
 *
 * =======================
 *
 * Usage, array notation (in an InputFilter as example):
 * $this->add([
 * 'name' => 'username',
 * 'required' => true,
 * 'filters' => [ ... ],
 * 'validators' => [
 *     [
 *         'name' => NoOtherObjectExists::class,
 *         'options' => [
 *             'fields' => 'username',
 *             'class' => User::class,
 *             'object_repository' => $this->getRepository(),
 *             'comparison_object' => [
 *                 'identifier' => 'id',
 *                 'id' => 2, //Make sure you pass along the identifying param
 *             ],
 *         ],
 *     ],
 * ]);
 *
 * Usage function notation:
 *
 * $validator = new Keet\Form\Validator\NoOtherObjectExists([
 *     'fields' => 'username',
 *     'class' => User::class,
 *     'object_repository' => $this->getEntityManager()->getRepository(User::class),
 *     'comparison_object' => [
 *         'identifier' => 'id',
 *         'id' => $router->match($request)->getParam('id'),
 *     ],
 * ]);
 */
class NoOtherObjectExists extends NoObjectExists
{
    /**
     * @param mixed $value
     *
     * @return bool
     * @throws \Exception
     */
    public function isValid($value)
    {
        if ( ! array_key_exists('fields', $this->getOptions())) {

            throw new \InvalidArgumentException(
                'Required option "property" not set for NoOtherObjectExists Validator.'
            );
        }

        if ( ! array_key_exists('class', $this->getOptions())) {

            throw new \InvalidArgumentException(
                'Required option "class" not set for NoOtherObjectExists Validator.'
            );
        }

        if ( ! array_key_exists('comparison_object', $this->getOptions())) {

            throw new \InvalidArgumentException(
                'Required option "comparison_object" not set for NoOtherObjectExists Validator.'
            );
        }

        if ( ! array_key_exists('identifier', $this->getOption('comparison_object'))) {

            throw new \InvalidArgumentException(
                'Required option "identifier" not set for NoOtherObjectExists Validator "comparison_object".'
            );
        }

        if ( ! array_key_exists('id', $this->getOption('comparison_object'))) {

            throw new \InvalidArgumentException(
                'Required option "id" not set for NoOtherObjectExists Validator "comparison_object".'
            );
        }

        $propertyName = $this->getOption('fields');
        $propertyClass = $this->getOption('class');

        $getterMethod = 'get' . ucfirst($propertyName);
        $cleanedValue = $this->cleanSearchValue($value);

        $comparisonObjectProperties = $this->getOption('comparison_object');
        $getIdentifier = 'get' . ucfirst($comparisonObjectProperties['identifier']);

        /** @var \object $comparisonObject */
        $comparisonObject = $this->objectRepository->findOneBy(
            [
                $comparisonObjectProperties['identifier'] => $comparisonObjectProperties['id'],
            ]
        );

        if ( ! method_exists($comparisonObject, $getterMethod)) {

            throw new \Exception(sprintf('Method %s() doesn\'t exist in class %s!', $getterMethod, $propertyClass));
        }

        $matches = $this->objectRepository->findBy($cleanedValue);
        if ($matches) {
            foreach ($matches as $index => $match) {
                // Check if the match has the same identifier as the object being edited. If it does continue, this is allowed.
                if (is_object($match)
                    && $match instanceof $propertyClass
                    && $match->$getIdentifier() === $comparisonObject->$getIdentifier()
                ) {

                    continue;
                }

                $this->error(self::ERROR_OBJECT_FOUND, $value);

                return false;
            }
        }

        return true;
    }
}