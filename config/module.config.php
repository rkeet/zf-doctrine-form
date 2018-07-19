<?php

namespace Keet\Form;

use Keet\Form\Factory\GenericDeleteFormFactory;
use Keet\Form\Factory\GenericDoctrineDeleteFormFactory;
use Keet\Form\Form\GenericDeleteForm;
use Keet\Form\Form\GenericDoctrineDeleteForm;
use Zend\I18n\Translator\TranslatorServiceFactory;

return [
    'form_elements'   => [
        'factories' => [
            GenericDeleteForm::class         => GenericDeleteFormFactory::class,
            GenericDoctrineDeleteForm::class => GenericDoctrineDeleteFormFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            'translator' => TranslatorServiceFactory::class,
        ],
    ],
    'view_manager'    => [
        'template_map' => [
            'keet/partials/form/genericDeleteForm'         => __DIR__ . '/../view/partials/generic-delete-form.phtml',
            'keet/partials/form/genericDoctrineDeleteForm' => __DIR__
                . '/../view/partials/generic-doctrine-delete-form.phtml',
        ],
    ],
];