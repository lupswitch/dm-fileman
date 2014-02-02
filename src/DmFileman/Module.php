<?php

namespace DmFileman;

use Zend\ServiceManager\ServiceManager;
use Zend\Mvc\Controller\ControllerManager;

class Module
{
    /**
     * Retrieve autoloader configuration
     *
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                )
            )
        );
    }

    /**
     * Retrieve module configuration
     *
     * @return array
     */
    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }

    /**
     * @return array
     */
    public function getServiceConfig()
    {
        return array(
            'factories'  => array(
                'DmFileman\View\Helper\UserText' => function () {
                    $userText = new \DmCommon\View\Helper\UserText;

                    $userText->addActions(\DmCommon\DefinedConstant\Action::getMessages());
                    $userText->addMessages(\DmCommon\DefinedConstant\Message::getMessages());
                    $userText->addMessages(\DmFileman\DefinedConstant\Message::getMessages());
                    $userText->addEntityNames(\DmFileman\DefinedConstant\EntityName::getMessages());

                    return $userText;
                },
                'DmFileman\Helper\Options' => function (ServiceManager $serviceManager) {
                    $options = $serviceManager->get('config');

                    $optionsHelper = new Helper\Options($options);

                    return $optionsHelper;
                },
                'DmFileman\InputFilter\UploadFile' => function (ServiceManager $serviceManager) {
                    /** @var Helper\Options $optionsHelper */
                    $optionsHelper = $serviceManager->get('DmFileman\Helper\Options');

                    $uploadFileFilter = new InputFilter\UploadFile();

                    $uploadFileFilter->setExtensions($optionsHelper->getExtensions());
                    $uploadFileFilter->setMaxSize($optionsHelper->getMaxSize());

                    return $uploadFileFilter;
                },
                'DmFileman\Service\FileManager' => function (ServiceManager $serviceManager) {
                    /** @var Helper\Options $config */
                    $options = $serviceManager->get('DmFileman\Helper\Options');

                    $factory = new Service\FileManager\Factory;

                    $fileManager = new Service\FileManager\FileManager($factory, $options->getFileManagerOptions());

                    return $fileManager;
                },
                'DmFileman\Service\Thumbnailer' => function (ServiceManager $serviceManager) {
                    /** @var Helper\Options $config */
                    $options = $serviceManager->get('DmFileman\Helper\Options');

                    /** @var \Imagine\Gd\Imagine $imagine */
                    $imagine = $serviceManager->get('Imagine\Gd\Imagine');

                    $factory = new Service\Thumbnailer\Factory();

                    $thumbnailer = new Service\Thumbnailer\Thumbnailer(
                        $imagine,
                        $factory,
                        $options->getThumbsOptions()
                    );

                    return $thumbnailer;
                },
            )
        );
    }

    /**
     * @return array
     */
    public function getControllerConfig()
    {
        return array(
            'factories' => array(
                'DmFileman\Controller\ListController'  => function (ControllerManager $controllerManager) {
                    /** @var ServiceManager $serviceManager */
                    $serviceManager = $controllerManager->getServiceLocator();

                    $fileManager    = $serviceManager->get('DmFileman\Service\FileManager');
                    $createDirForm  = new Form\CreateDirectoryForm();
                    $uploadFileForm = new Form\UploadFileForm();
                    $deleteFileForm = new Form\DeleteFileForm();

                    $createDirForm->setInputFilter(new InputFilter\CreateDirectory());
                    /** @var InputFilter\UploadFile $inputFileFilter */
                    $inputFileFilter = $serviceManager->get('DmFileman\InputFilter\UploadFile');
                    $uploadFileForm->setInputFilter($inputFileFilter);
                    $deleteFileForm->setInputFilter(new InputFilter\DeleteFile());

                    /** @var InputFilter\UploadFile $uploadFileFilter */
                    $uploadFileFilter = $uploadFileForm->getInputFilter();
                    $uploadFileFilter->setFileInput(new \Zend\InputFilter\FileInput());

                    $controller = new Controller\ListController(
                        $fileManager,
                        $createDirForm,
                        $uploadFileForm,
                        $deleteFileForm
                    );

                    return $controller;
                },
                'DmFileman\Controller\DeleteFileController'  => function (ControllerManager $controllerManager) {
                    /** @var ServiceManager $serviceManager */
                    $serviceManager = $controllerManager->getServiceLocator();

                    $fileManager    = $serviceManager->get('DmFileman\Service\FileManager');
                    $deleteFileForm = new Form\DeleteFileForm();
                    $thumbsConfig   = $serviceManager->get('DmFileman\Service\Thumbnailer');
                    $userText       = $serviceManager->get('DmFileman\View\Helper\UserText');

                    $deleteFileForm->setInputFilter(new InputFilter\DeleteFile());

                    $controller = new Controller\DeleteFileController(
                        $fileManager,
                        $deleteFileForm,
                        $thumbsConfig,
                        $userText
                    );

                    return $controller;
                },
                'DmFileman\Controller\UploadFileController'  => function (ControllerManager $controllerManager) {
                    /** @var ServiceManager $serviceManager */
                    $serviceManager = $controllerManager->getServiceLocator();

                    $fileManager    = $serviceManager->get('DmFileman\Service\FileManager');
                    $uploadFileForm = new Form\UploadFileForm();
                    $thumbsConfig   = $serviceManager->get('DmFileman\Service\Thumbnailer');
                    $userText       = $serviceManager->get('DmFileman\View\Helper\UserText');

                    /** @var InputFilter\UploadFile $inputFileFilter */
                    $inputFileFilter = $serviceManager->get('DmFileman\InputFilter\UploadFile');
                    $uploadFileForm->setInputFilter($inputFileFilter);

                    /** @var InputFilter\UploadFile $uploadFileFilter */
                    $uploadFileFilter = $uploadFileForm->getInputFilter();
                    $uploadFileFilter->setFileInput(new \Zend\InputFilter\FileInput());

                    $controller = new Controller\UploadFileController(
                        $fileManager,
                        $uploadFileForm,
                        $thumbsConfig,
                        $userText
                    );

                    return $controller;
                },
                'DmFileman\Controller\CreateDirectoryController'  => function (ControllerManager $controllerManager) {
                    /** @var ServiceManager $serviceManager */
                    $serviceManager = $controllerManager->getServiceLocator();

                    $fileManager   = $serviceManager->get('DmFileman\Service\FileManager');
                    $createDirForm = new Form\CreateDirectoryForm();
                    $thumbsConfig  = $serviceManager->get('DmFileman\Service\Thumbnailer');
                    $userText       = $serviceManager->get('DmFileman\View\Helper\UserText');

                    $createDirForm->setInputFilter(new InputFilter\CreateDirectory());

                    $controller = new Controller\CreateDirectoryController(
                        $fileManager,
                        $createDirForm,
                        $thumbsConfig,
                        $userText
                    );

                    return $controller;
                },
            )
        );
    }
}
