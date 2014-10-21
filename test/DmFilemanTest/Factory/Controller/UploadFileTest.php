<?php

namespace DmFilemanTest\Factory\Controller;

use DmFileman\Factory\Controller\UploadFile;

use DmTest\ServiceManager\ServiceLocatorDummy;

class UploadFileTest extends \PHPUnit_Framework_TestCase
{
    /** @var UploadFile */
    private $sut;

    public function setUp()
    {
        $this->sut = new UploadFile;
    }

    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy();

        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager\FileManager');
        $serviceLocator->set('DmFileman\Service\FileManager', $fileManager);

        $thumbnailer = $serviceLocator->get('DmFileman\Service\Thumbnailer\Thumbnailer');
        $serviceLocator->set('DmFileman\Service\Thumbnailer', $thumbnailer);

        $userText = $serviceLocator->get('DmCommon\View\Helper\UserText');
        $serviceLocator->set('DmFileman\View\Helper\UserText', $userText);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Controller\UploadFileController', $result);
    }
}
