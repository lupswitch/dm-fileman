<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\DeleteFileController;

class DeleteFileControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeleteFileController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $deleteFileFormMock;

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

        $this->deleteFileFormMock = $this->getMockBuilder('DmFileman\Form\DeleteFileForm')
            ->setMethods(['build', 'getInputFilter'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new DeleteFileController(
            $this->fileManagerMock,
            $this->deleteFileFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\DeleteFileController
     */
    public function testDeleteAction()
    {
        $this->markTestIncomplete();
    }
}
