<?php

namespace DmFileman\Factory\Controller;

use Zend\InputFilter\FileInput;
use Zend\Mvc\Controller\ControllerManager;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

use DmFileman\Controller\ListController;
use DmFileman\Form\DeleteFileForm;
use DmFileman\Form\CreateDirectoryForm;
use DmFileman\Form\UploadFileForm;
use DmFileman\InputFilter\CreateDirectory as CreateDirectoryInputFilter;
use DmFileman\InputFilter\DeleteFile as DeleteFileInputFilter;
use DmFileman\InputFilter\UploadFile as UploadFileInputFilter;
use DmFileman\Service\FileManager\FileManager as FileManagerService;

class ListFactory implements FactoryInterface
{
    /**
     * @param ServiceLocatorInterface $serviceLocator
     *
     * @return ListController
     */
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        if ($serviceLocator instanceof ControllerManager) {
            $serviceLocator = $serviceLocator->getServiceLocator();
        }

        /** @var FileManagerService $fileManager */
        $fileManager    = $serviceLocator->get('DmFileman\Service\FileManager');
        $createDirForm  = new CreateDirectoryForm();
        $uploadFileForm = new UploadFileForm();
        $deleteFileForm = new DeleteFileForm();

        $createDirForm->setInputFilter(new CreateDirectoryInputFilter());

        /** @var UploadFileInputFilter $inputFileFilter */
        $inputFileFilter = $serviceLocator->get('DmFileman\InputFilter\UploadFile');
        $uploadFileForm->setInputFilter($inputFileFilter);
        $deleteFileForm->setInputFilter(new DeleteFileInputFilter());

        /** @var UploadFileInputFilter $uploadFileFilter */
        $uploadFileFilter = $uploadFileForm->getInputFilter();
        $uploadFileFilter->setFileInput(new FileInput());

        $controller = new ListController(
            $fileManager,
            $createDirForm,
            $uploadFileForm,
            $deleteFileForm
        );

        return $controller;
    }
}
