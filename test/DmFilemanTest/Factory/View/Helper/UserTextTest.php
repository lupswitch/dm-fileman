<?php

namespace DmFilemanTest\Factory\View\Helper;

use DmFileman\Factory\View\Helper\UserText;

use DmTest\ServiceManager\ServiceLocatorDummy;

class UserTextTest extends \PHPUnit_Framework_TestCase
{
    /** @var UserText */
    private $sut;

    public function setUp()
    {
        $this->sut = new UserText;
    }

    public function testCreateService()
    {
        $serviceLocator = new ServiceLocatorDummy($this);

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmCommon\View\Helper\UserText', $result);
    }
}
