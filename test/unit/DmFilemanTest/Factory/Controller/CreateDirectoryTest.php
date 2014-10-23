<?php

namespace DmFilemanTest\Factory\Controller;

use DmFileman\Factory\Controller\CreateDirectory;

use DmTest\ServiceManager\ServiceLocatorDummy;

class CreateDirectoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateDirectory */
    private $sut;

    public function setUp()
    {
        $this->sut = new CreateDirectory;
    }

    /**
     * @covers DmFileman\Factory\Controller\CreateDirectory
     */
    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy($this);

        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager\FileManager');
        $serviceLocator->set('DmFileman\Service\FileManager', $fileManager);

        $thumbnailer = $serviceLocator->get('DmFileman\Service\Thumbnailer\Thumbnailer');
        $serviceLocator->set('DmFileman\Service\Thumbnailer', $thumbnailer);

        $userText = $serviceLocator->get('DmCommon\View\Helper\UserText');
        $serviceLocator->set('DmFileman\View\Helper\UserText', $userText);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Controller\CreateDirectoryController', $result);
    }
}
