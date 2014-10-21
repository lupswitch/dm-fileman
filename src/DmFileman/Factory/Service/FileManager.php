<?php

namespace DmFileman\Factory\Service;

use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmFileman\Helper\Options as OptionsHelper;
use DmFileman\Service\FileManager\FileManager as FileManagerService;
use DmFileman\Service\FileManager\Factory as FileManagerFactory;

class FileManager implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return FileManagerService
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        /** @var OptionsHelper $config */
        $options = $serviceLocator->get('DmFileman\Helper\Options');

        $factory = new FileManagerFactory;

        $fileManager = new FileManagerService($factory, $options->getFileManagerOptions());

        return $fileManager;
    }
}
