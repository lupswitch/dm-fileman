<?php

namespace DmFilemanTest\Factory\Helper;

use DmFileman\Factory\Helper\Option;

use DmTest\ServiceManager\ServiceLocatorDummy;

class OptionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Option */
    private $sut;

    public function setUp()
    {
        $this->sut = new Option;
    }

    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy($this);

        $serviceLocator->set('config', []);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\Helper\Options', $result);
    }
}
