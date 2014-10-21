<?php

namespace DmFilemanTest\Factory\Controller;

use DmFileman\Factory\Controller\ListFactory;

use DmTest\ServiceManager\ServiceLocatorDummy;

class ListFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ListFactory */
    private $sut;

    public function setUp()
    {
        $this->sut = new ListFactory;
    }

    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy();

        $fileManager = $serviceLocator->get('DmFileman\Service\FileManager\FileManager');
        $serviceLocator->set('DmFileman\Service\FileManager', $fileManager);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Controller\ListController', $result);
    }
}
