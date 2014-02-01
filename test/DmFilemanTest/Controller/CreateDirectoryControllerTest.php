<?php

namespace DmFilemanTest\Controller;

use DmFileman\Controller\CreateDirectoryController;

class CreateDirectoryControllerTest extends \PHPUnit_Framework_TestCase
{
    /** @var CreateDirectoryController */
    protected $sut;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $fileManagerMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $createDirFormMock;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $uploadFileFormMock;

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

        $this->createDirFormMock = $this->getMockBuilder('DmFileman\Form\CreateDirectoryForm')
            ->setMethods(['build', 'getInputFilter'])
            ->getMock();

        $this->thumbnailerMock = $this->getMockBuilder('DmFileman\Service\Thumbnailer\Thumbnailer')
            ->setMethods(['resize'])
            ->disableOriginalConstructor()
            ->getMock();

        $this->userTextMock = $this->getMockBuilder('DmFileman\View\Helper\UserText')
            ->setMethods(['getMessage'])
            ->getMock();

        $this->sut = new CreateDirectoryController(
            $this->fileManagerMock,
            $this->createDirFormMock,
            $this->thumbnailerMock,
            $this->userTextMock
        );
    }

    /**
     * @covers DmFileman\Controller\CreateDirectoryController
     */
    public function testCreateAction()
    {
        $this->markTestIncomplete();
    }
}
