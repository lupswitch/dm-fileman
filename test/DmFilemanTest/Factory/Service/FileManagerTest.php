<?php

namespace DmFilemanTest\Factory\Service;

use DmFileman\Factory\Service\FileManager;

use DmTest\ServiceManager\ServiceLocatorDummy;

class FileManagerTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileManager */
    private $sut;

    public function setUp()
    {
        $this->sut = new FileManager;
    }

    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy($this);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Service\FileManager\FileManager', $result);
    }
}
