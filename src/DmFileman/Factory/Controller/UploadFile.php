<?php

namespace DmFileman\Factory\Controller;

use Zend\InputFilter\FileInput;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmCommon\View\Helper\UserText;

use DmFileman\Controller\UploadFileController;
use DmFileman\Form\UploadFileForm;
use DmFileman\InputFilter\UploadFile as UploadFileInputFilter;
use DmFileman\Service\FileManager as FileManagerService;
use DmFileman\Service\Thumbnailer as ThumbnailerService;

class UploadFile implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return UploadFileController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ControllerManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var FileManagerService $fileManager */
        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager');

        $uploadFileForm = new UploadFileForm();

        /** @var ThumbnailerService $thumbsConfig */
        $thumbsConfig = $serviceLocator->get('DmFileman\Service\Thumbnailer');

        /** @var UserText $userText */
        $userText = $serviceLocator->get('DmFileman\View\Helper\UserText');

        /** @var UploadFileInputFilter $inputFileFilter */
        $inputFileFilter = $serviceLocator->get('DmFileman\InputFilter\UploadFile');

        $uploadFileForm->setInputFilter($inputFileFilter);

        /** @var UploadFile $uploadFileFilter */
        $uploadFileFilter = $uploadFileForm->getInputFilter();
        $uploadFileFilter->setFileInput(new FileInput());

        $controller = new UploadFileController(
            $fileManager,
            $uploadFileForm,
            $thumbsConfig,
            $userText
        );

        return $controller;
    }
}
