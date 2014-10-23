<?php

namespace DmFilemanTest\Factory\Service;

use DmFileman\Factory\Service\Thumbnailer;

use DmTest\ServiceManager\ServiceLocatorDummy;

class ThumbnailerTest extends \PHPUnit_Framework_TestCase
{
    /** @var Thumbnailer */
    private $sut;

    public function setUp()
    {
        $this->sut = new Thumbnailer;
    }

    /**
     * @covers DmFileman\Factory\Service\Thumbnailer
     */
    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy($this);

        $optionsStub = $this->getMockBuilder('DmFileman\Helper\Options')
            ->setMethods(['getThumbsOptions'])
            ->disableOriginalConstructor()
            ->getMock();
        $optionsStub->expects($this->any())->method('getThumbsOptions')->willReturn([]);
        $serviceLocator->set('DmFileman\Helper\Options', $optionsStub);

        $imagineStub = $this->getMockBuilder('Imagine\Gd\Imagine')
            ->setMethods([])
            ->disableAutoload()
            ->getMock();
        $serviceLocator->set('Imagine\Gd\Imagine', $imagineStub);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Service\Thumbnailer\Thumbnailer', $result);
    }
}
