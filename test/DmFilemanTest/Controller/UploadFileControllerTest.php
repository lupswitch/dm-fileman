<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\UploadFileController;

class UploadFileControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var UploadFileController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $uploadFileFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $thumbnailerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $userTextMock;

    public function setUp()
    {
        $this->fileManagerMock = $this->getMockBuilder('DmFileman\Service\FileManager\FileManager')
            ->setMethods(['setCurrentPath', 'getOrigDir', 'getList'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->uploadFileFormMock = $this->getMockBuilder('DmFileman\Form\UploadFileForm')
            ->setMethods(['build', 'getMessages', 'getInputFilter'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new UploadFileController(
            $this->fileManagerMock,
            $this->uploadFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\UploadFileController
     */
    public function testUploadAction()
    {
        $this->markTestIncomplete();
    }
}
