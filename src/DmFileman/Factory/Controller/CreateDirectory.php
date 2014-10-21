<?php

namespace DmFileman\Factory\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmCommon\View\Helper\UserText;

use DmFileman\Controller\CreateDirectoryController;
use DmFileman\Form\CreateDirectoryForm;
use DmFileman\InputFilter\CreateDirectory as CreateDirectoryInputFilter;
use DmFileman\Service\FileManager\FileManager as FileManagerService;
use DmFileman\Service\Thumbnailer\Thumbnailer as ThumbnailerService;

class CreateDirectory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return CreateDirectoryController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ControllerManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        $createDirForm = new CreateDirectoryForm();

        /** @var FileManagerService $fileManager */
        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager');

        /** @var ThumbnailerService $thumbsConfig */
        $thumbsConfig = $serviceLocator->get('DmFileman\Service\Thumbnailer');

        /** @var UserText $userText */
        $userText = $serviceLocator->get('DmFileman\View\Helper\UserText');

        $createDirForm->setInputFilter(new CreateDirectoryInputFilter());

        $controller = new CreateDirectoryController(
            $fileManager,
            $createDirForm,
            $thumbsConfig,
            $userText
        );

        return $controller;
    }
}
