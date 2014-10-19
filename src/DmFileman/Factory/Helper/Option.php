<?php

namespace DmFileman\Factory\Helper;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmFileman\Helper\Options as OptionsHelper;

class Option implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return OptionsHelper
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var array $options */
        $options = $serviceLocator->get('config');

        $optionsHelper = new OptionsHelper($options);

        return $optionsHelper;
    }
}
