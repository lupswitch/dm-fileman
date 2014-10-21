<?php

namespace DmFilemanTest\Factory\InputFilter;

use DmFileman\Factory\InputFilter\UploadFile;

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

        $result = $this->sut->createService($serviceLocator);

        $this->assertInstanceOf('DmFileman\InputFilter\UploadFile', $result);
    }
}
