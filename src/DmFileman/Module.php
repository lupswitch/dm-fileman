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
                'Zend\Cache\Storage\StorageInterface' => function (ServiceManager $serviceManager) {
                    /** @var Helper\Options $options */
                    $options = $serviceManager->get('DmFileman\Helper\Options');

                    $cacheStorage = \Zend\Cache\StorageFactory::factory($options->getCacheStorage());

                    return $cacheStorage;
                },
                'DmFileman\Helper\Options' => function (ServiceManager $serviceManager) {
                    $options = $serviceManager->get('config');

                    $optionsHelper = new Helper\Options($options);

                    return $optionsHelper;
                },
                'DmFileman\Service\FileManager' => function (ServiceManager $serviceManager) {
                    $cacheStorage = $serviceManager->get('Zend\Cache\Storage\StorageInterface');

                    $config = $serviceManager->get('config')['filemanager'];

                    $factory = new Service\FileManager\Factory;

                    $fileManager = new Service\FileManager\FileManager($cacheStorage, $factory, $config);

                    return $fileManager;
                },
                'DmFileman\Service\Thumbnailer' => function (ServiceManager $serviceManager) {
                    $thumbConfig = $serviceManager->get('config')['filemanager']['thumbs'];

                    /** @var \Imagine\Gd\Imagine $imagine */
                    $imagine = $serviceManager->get('Imagine\Gd\Imagine');

                    $factory = new Service\Thumbnailer\Factory();

                    $thumbnailer = new Service\Thumbnailer\Thumbnailer($imagine, $factory, $thumbConfig);

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
                'DmFileman\Controller\FileManagerController'  => function (ControllerManager $controllerManager) {
                    /** @var ServiceManager $serviceManager */
                    $serviceManager = $controllerManager->getServiceLocator();

                    $fileManager = $serviceManager->get('DmFileman\Service\FileManager');
                    $createFileForm = new Form\CreateDirectoryForm();
                    $uploadFileForm = new Form\UploadFileForm();
                    $deleteFileForm = new Form\DeleteFileForm();
                    $thumbsConfig = $serviceManager->get('DmFileman\Service\Thumbnailer');
                    $userText = new View\Helper\UserText();

                    $createFileForm->setInputFilter(new InputFilter\CreateDirectory());
                    $uploadFileForm->setInputFilter(new InputFilter\UploadFile());
                    $deleteFileForm->setInputFilter(new InputFilter\DeleteFile());

                    /** @var InputFilter\UploadFile $uploadFileFilter */
                    $uploadFileFilter = $uploadFileForm->getInputFilter();
                    $uploadFileFilter->setFileInput(new \Zend\InputFilter\FileInput());

                    $controller = new Controller\FileManagerController(
                        $fileManager,
                        $createFileForm,
                        $uploadFileForm,
                        $deleteFileForm,
                        $thumbsConfig,
                        $userText
                    );

                    return $controller;
                },
            )
        );
    }
}
