<?php

namespace DmFileman\Factory\Controller;

use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmCommon\View\Helper\UserText;

use DmFileman\Controller\DeleteFileController;
use DmFileman\Form\DeleteFileForm;
use DmFileman\InputFilter\DeleteFile as DeleteFileInputFilter;
use DmFileman\Service\FileManager\FileManager as FileManagerService;
use DmFileman\Service\Thumbnailer\Thumbnailer as ThumbnailerService;

class DeleteFile implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return DeleteFileController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ControllerManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var FileManagerService $fileManager */
        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager');

        $deleteFileForm = new DeleteFileForm();

        /** @var ThumbnailerService $thumbsConfig */
        $thumbsConfig = $serviceLocator->get('DmFileman\Service\Thumbnailer');

        /** @var UserText $userText */
        $userText = $serviceLocator->get('DmFileman\View\Helper\UserText');

        $deleteFileForm->setInputFilter(new DeleteFileInputFilter());

        $controller = new DeleteFileController(
            $fileManager,
            $deleteFileForm,
            $thumbsConfig,
            $userText
        );

        return $controller;
    }
}
