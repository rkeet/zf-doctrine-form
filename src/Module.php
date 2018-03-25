<?php

namespace Keet\Form;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\ConfigProviderInterface;

class Module implements ConfigProviderInterface, AutoloaderProviderInterface
{
    /**
     * @return array
     */
    public function getConfig(): array
    {
        $config = [];

        foreach (glob(dirname(__DIR__) . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . '*.php') as $filename) {
            $config = array_merge_recursive($config, include $filename);
        }

        return $config;
    }

    /**
     * @return array
     */
    public function getAutoloaderConfig(): array
    {
        return [
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    __NAMESPACE__ => __DIR__,
                ],
            ],
        ];
    }
}